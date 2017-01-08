<?php
// src/Application/CoreBundle/Menu/MenuBuilder.php

namespace Application\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAware;
use minsal\centeot\alimentosBundle\Entity\AlimEmpresa;
use minsal\centeot\alimentosBundle\Entity\AlimPersonaNatural;

class MenuBuilder extends ContainerAware {

  private $menu;
  private $user;
  private $groups;
  private $roles = array();

  private $cat = array('AE' => array('label' => 'Administracion Empresa', 'icon' => 'glyphicon glyphicon-edit'),
  'ES' => array('label' => 'Establecimientos / Bodegas / Plantas', 'icon' => 'glyphicon glyphicon-home'),
  'EP' => array('label' => 'Empresas y Personas Naturales', 'icon' => 'glyphicon glyphicon-stats'),
  'PR' => array('label' => 'Productos', 'icon' => 'glyphicon glyphicon-file'),
  'SL' => array('label' => 'Solicitudes Importacion', 'icon' => 'glyphicon glyphicon-edit'),
  'CV' => array('label' => 'Certificado Libre Venta', 'icon' => 'glyphicon glyphicon-time'),
  'SI' => array('label' => 'Solicitudes Importacion', 'icon' => 'glyphicon glyphicon-time'),
  'CT' => array('label' => 'Catalogos', 'icon' => 'glyphicon glyphicon-cog'),
  'TM' => array('label' => 'Tramitadores', 'icon' => 'glyphicon glyphicon-briefcase'),
  'US' => array('label' => 'Usuario', 'icon' => 'glyphicon glyphicon-user'),
  'RS' => array('label' => 'Registro Sanitario', 'icon' => 'glyphicon glyphicon-user'),);

  //    private $staticEmpresa = array('EP' => array('label' => 'Empresa', 'icon' => 'glyphicon glyphicon-stats'),);
  public function mainMenu(FactoryInterface $factory, array $options) {

    $user = $this->container->get('security.context')->getToken()->getUser();
    $this->menu = $factory->createItem('root');
    $this->menu->setChildrenAttribute('class', 'nav navbar-nav');

    $admin = $options['admin'];
    $this->user =$options['user'];
    $this->groups = $this->user->getGroups();
    $this->roles = $this->getRolesUser();

  //  $this->createStaticMenuTramitadores($this->user);



    /* Creacion del Menu dinamico */
    foreach ($admin as $key => $value) {

      if ($key == "sonata_user")
      $key = "US-1-Gestión de Usuario";

      list($category, $level, $label) = explode("-", $key);
      $this->createDropDownMenu($value['items'], $this->cat[$category]['label'], $label, $level, $this->cat[$category]['icon']);
    }

    return $this->menu;
  }

  /*Obtiene los roles del usuario (del sistema actual), en base a sus grupos*/
  private function getRolesUser() {
    $system = $this->getSystemSelected();
    $rol_array = array();

    foreach($this->groups as $group) {

      $split = explode('_', $group->getName());
      $prefix_group = $split[0];
      if($prefix_group == $system) {
        $rol_array[] = $group->getRoles();
      }
    }

    return $rol_array;

  }

  private function getSystemSelected() {
    $request = $this->container->get('request');
    $number = $request->getSession()->get('_moduleSelection');
    $system = "";


    switch ($number) {
      case '1':
      $system = "alim";
      break;
      case '2':
      $system = "alcohol";
      break;
      default:
      break;
    }

    return $system;
  }

  private function createDropDownMenu($object, $catLabel, $label, $level, $icon) {
    $contMenu = $this->countItemsGranted($object);

    if (count($contMenu) != 0) {
      if (!$this->menu[$catLabel]) {
        $this->menu->addChild($catLabel)->setAttribute('dropdown', true)->setAttribute('icon', $icon);
      }

      switch ($level) {
        case '1':
        foreach ($contMenu as $keya => $itema) {
          $this->menu[$catLabel]->addChild($itema['label'], array('uri' => $itema['url']));
        }
        break;
        case '2':
        if (!$this->menu[$catLabel][$label]) {
          $this->menu[$catLabel]->addChild($label)->setAttribute('dropdown', true);
        }

        foreach ($contMenu as $keyb => $itemb) {
          $this->menu[$catLabel][$label]->addChild($itemb['label'], array('uri' => $itemb['url']));
        }
        break;

        default:

        break;
      }
    }
  }

  private function countItemsGranted(array $items) {
    $array = array();

    foreach ($items as $key => $object) {

      if ($object->hasroute('list') && $object->isGranted('LIST') && $this->validateOptionSystem($object) ) {

        if ($object->getLabel() == "groups") {
          $array[] = array('label' => "Grupos", 'url' => $object->generateUrl('list'));
        } else {
          $array[] = array('label' => $object->getLabel(), 'url' => $object->generateUrl('list'));
        }
      }
    }

    return $array;
  }

  /*Valida que la opcion del sistema a mostrar en el menu, corresponda con los
  roles otorgados al usuario, para el sistema en el que se haya logueado*/
  private function validateOptionSystem($object) {
    $baseRole = 'ROLE_' . str_replace('.', '_', strtoupper($object->getCode()));

    foreach ($this->roles as $rol) {
      foreach($rol as $r) {

        $privilege = strrchr($r, '_');
        $roleWithuotPrivilege = strstr($r, $privilege, true);

        if($baseRole == $roleWithuotPrivilege ) {
          return true;
        }

      }
    }
    return false;
  }

  private function createStaticMenuTramitadores($user) {
    if ($user->hasGroup('alim_tramitadorRepresentante_avalado') || $user->hasGroup('alim_tramitadorApoderado_avalado') ){
          $request = $this->container->get('request');
          $session = $request->getSession();
          $empresa = $session->get('empresa');/*Cuando se trata de un tramitador, la variable de sesion nos indica la empresa o persona natural que selecciono dicho tramitador*/


          if( $empresa instanceof AlimEmpresa ){
              $this->menu->addChild('Empresa')->setAttribute('dropdown', true)->setAttribute('icon', 'glyphicon glyphicon-stats');
              /*Empresa */
              $this->menu['Empresa']->addChild('Empresa')->setAttribute('dropdown', true);
              $this->menu['Empresa']['Empresa']->addChild('Datos Generales', array('route' => 'admin_centeot_alimentos_alimempresa_list'));
              $this->menu['Empresa']['Empresa']->addChild('Establecimientos / Bodegas / Planta', array('route' => 'admin_centeot_alimentos_alimbodega_list'));
              $this->menu['Empresa']['Empresa']->addChild('Representante Legal', array('route' => 'admin_centeot_alimentos_representantelegal_list'));
              $this->menu['Empresa']['Empresa']->addChild('Apoderados', array('route' => 'admin_centeot_alimentos_apoderado_list'));

          }elseif( $empresa instanceof AlimPersonaNatural ){
              $this->menu->addChild('Persona Natural')->setAttribute('dropdown', true)->setAttribute('icon', 'glyphicon glyphicon-stats');
              /*Empresa */
              $this->menu['Persona Natural']->addChild('Persona Natural')->setAttribute('dropdown', true);
              $this->menu['Persona Natural']['Persona Natural']->addChild('Datos Generales', array('route' => 'admin_centeot_alimentos_alimpersonanatural_list'));
              $this->menu['Persona Natural']['Persona Natural']->addChild('Establecimientos / Bodegas / Planta', array('route' => 'admin_centeot_alimentos_alimbodega_list'));
              $this->menu['Persona Natural']['Persona Natural']->addChild('Apoderados', array('route' => 'admin_centeot_alimentos_apoderado_list'));
          }
  }
 }
}
