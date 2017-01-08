<?php
// src/Application/CoreBundle/Security/ModuleProvider.php
namespace Application\CoreBundle\Security;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;

class ModuleProvider {
	private $module;
	private $user;
	private $dbal;

	public function __construct(UserInterface $user, $sistema, $dbal) {
		$this->user   =	$user;
		$this->sistema = $sistema;
		$this->dbal = $dbal;
	}

	public function validateModule() {
		if($this->user->hasRole('ROLE_SUPER_ADMIN')){
			return true;
		}

		$sistema = $this->sistema;
		$patron = "NONE";

        switch ($sistema) {
            case '1':
            	$patron = "alim";
                break;
            case '2':
            	$patron = "alcohol";
                break;
            case '3':
            	$patron = "quimico";
                break;                
            default:
                break;
        }		


		$userId = $this->user->getId();
		$sql = "SELECT t01.id, t01.username, t03.name as Grupo
				FROM fos_user_user t01
				INNER JOIN fos_user_user_group t02 ON (t01.id = t02.user_id)
				INNER JOIN fos_user_group t03 ON (t03.id = t02.group_id)
				WHERE t03.name like '$patron%' AND t01.id = $userId";


		$stm = $this->dbal->prepare($sql);
		$stm->execute();
        $result = $stm->fetchAll();

        if($result) {
        	return true;
        }

        return false;
	}
}