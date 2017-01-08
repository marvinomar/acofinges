<?php
// src/Application/CoreBundle/Security/User/UserProvider.php
namespace Application\CoreBundle\Security\User;

use FOS\UserBundle\Security\UserProvider as FOSProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;

class UserProvider extends FOSProvider {
    private $entityManager;
    private $container;

    public function __construct(UserManagerInterface $userManager, ContainerInterface $container, EntityManager $entityManager) {
        parent::__construct($userManager);

        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username) {
        $user = $this->findUser($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }
}