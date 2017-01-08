<?php
// src/Application/CoreBundle/Security/Firewall/ListenerInterface.php
namespace Application\CoreBundle\Security\Firewall;

use Symfony\Component\Security\Http\Firewall\ListenerInterface as SymfonyListenerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\SessionUnavailableException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Application\CoreBundle\Security\Authentication\Handler\AuthenticationHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListenerInterface implements SymfonyListenerInterface {

    protected $options;
    protected $logger;
    protected $httpUtils;
    protected $authenticationManager;
    protected $providerKey;
    protected $httpKernel;

    private $securityContext;
    private $sessionStrategy;
    private $dispatcher;
    private $authenticationHandler;
    private $container;


    /**
     * Constructor.
     *
     * @param SecurityContextInterface               $securityContext       A SecurityContext instance
     * @param AuthenticationManagerInterface         $authenticationManager An AuthenticationManagerInterface instance
     * @param SessionAuthenticationStrategyInterface $sessionStrategy
     * @param HttpUtils                              $httpUtils             An HttpUtilsInterface instance
     * @param string                                 $providerKey
     * @param array                                  $options               An array of options for the processing of a
     *                                                                      successful, or failed authentication attempt
     * @param LoggerInterface                        $logger                A LoggerInterface instance
     * @param EventDispatcherInterface               $dispatcher            An EventDispatcherInterface instance
     *
     * @throws InvalidArgumentException
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $providerKey, HttpUtils $httpUtils, SessionAuthenticationStrategyInterface $sessionStrategy, EventDispatcherInterface $dispatcher = null, LoggerInterface $logger = null, HttpKernelInterface $httpKernel, ContainerInterface $container, array $options = array()) {
        if (empty($providerKey)) {
            throw new InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->securityContext       = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->providerKey           = $providerKey;
        $this->httpUtils             = $httpUtils;
        $this->sessionStrategy       = $sessionStrategy;
        $this->dispatcher            = $dispatcher;
        $this->logger                = $logger;
        $this->httpKernel            = $httpKernel;
        $this->container             = $container;
        $autheHandlerOptions         = array(
                                        'login_path' => '/admin/login',
                                        'default_target_path' => '/admin/dashboard',
                                        '_moduleSelection' => $this->container->get('request')->get('_moduleSelection') ? $this->container->get('request')->get('_moduleSelection') : null
                                        );
        //Customizar esta linea
        //$autheHandlerOptions = array_merge(array('after_login_path' => '/siaps/verify/medicservice'), $autheHandlerOptions);

        $this->authenticationHandler = new AuthenticationHandler($this->httpKernel, $this->httpUtils, $this->logger, $autheHandlerOptions,$this->container);
        $this->authenticationHandler->setProviderKey($providerKey);
        $this->options = array_merge(array(
            'check_path'                     => '/admin/login_check',
        ), $options);
    }

    /**
     * Handles login request.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     * @return void
     */
    public function handle(GetResponseEvent $event) {
        $request = $event->getRequest();

        if (!$this->requiresAuthentication($request)) {
            return;
        }

        if (!$request->hasSession()) {
            throw new RuntimeException('This authentication method requires a session.');
        }

        try {
            if (!$request->hasPreviousSession()) {
                throw new SessionUnavailableException('Your session has timed out, or you have disabled cookies.');
            }

            if (null === $returnValue = $this->attemptAuthentication($request)) {
                return;
            }

            if ($returnValue instanceof TokenInterface) {
                $this->sessionStrategy->onAuthentication($request, $returnValue);

                $response = $this->onSuccess($event, $request, $returnValue);
            } elseif ($returnValue instanceof Response) {
                $response = $returnValue;
            } else {
                throw new RuntimeException('attemptAuthentication() must either return a Response, an implementation of TokenInterface, or null.');
            }
        } catch (AuthenticationException $e) {
            $response = $this->onFailure($event, $request, $e);
        }

        $event->setResponse($response);
    }

    /**
     * Whether this request requires authentication.
     *
     * The default implementation only processes requests to a specific path,
     * but a subclass could change this to only authenticate requests where a
     * certain parameters is present.
     *
     * @param Request $request
     *
     * @return Boolean
     */
    protected function requiresAuthentication(Request $request) {
        if (!$request->isMethod('POST')) {
            return false;
        }

        return $this->httpUtils->checkRequestPath($request, $this->options['check_path']);
    }

    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request) {
        $username = trim($request->get('_username', null, true));
        $password = $request->get('_password', null, true);

        $request->getSession()->set(SecurityContextInterface::LAST_USERNAME, $username);

        return $this->authenticationManager->authenticate(new UsernamePasswordToken($username, $password, $this->providerKey));
    }

    private function onFailure(GetResponseEvent $event, Request $request, AuthenticationException $failed) {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('Authentication request failed: %s', $failed->getMessage()));
        }

        $token = $this->securityContext->getToken();
        if ($token instanceof UsernamePasswordToken && $this->providerKey === $token->getProviderKey()) {
            $this->securityContext->setToken(null);
        }

        $response = $this->authenticationHandler->onAuthenticationFailure($request, $failed);

        if (!$response instanceof Response) {
            throw new RuntimeException('Authentication Failure Handler did not return a Response.');
        }

        return $response;
    }

    private function onSuccess(GetResponseEvent $event, Request $request, TokenInterface $token) {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('User "%s" has been authenticated successfully', $token->getUsername()));
        }

        $this->securityContext->setToken($token);

        $session = $request->getSession();
        $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        $session->remove(SecurityContextInterface::LAST_USERNAME);

        if (null !== $this->dispatcher) {
            $loginEvent = new InteractiveLoginEvent($request, $token);
            $this->dispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
        }

        $response = $this->authenticationHandler->onAuthenticationSuccess($request, $token);

        if (!$response instanceof Response) {
            throw new RuntimeException('Authentication Success Handler did not return a Response.');
        }

        return $response;
    }
}