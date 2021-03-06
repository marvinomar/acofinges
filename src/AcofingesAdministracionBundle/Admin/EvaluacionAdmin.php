<?php

namespace AcofingesAdministracionBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class EvaluacionAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('resultado')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('resultado')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            //->add('id')
            ->add('resultado')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('resultado')
        ;
    }

    public function getTemplate($name) {
    switch ($name) {
        case 'edit':
            return 'AcofingesAdministracionBundle:custom:Evaluacion/flujo1.html.twig';
            break;
        case 'create':
            return 'AcofingesAdministracionBundle:custom:Evaluacion/flujo1.html.twig';
            break;
        case 'flujo1':
            return 'AcofingesAdministracionBundle:custom:Evaluacion/flujo1.html.twig';
            break;
        case 'flujo2':
            return 'AcofingesAdministracionBundle:custom:Evaluacion/flujo2.html.twig';
            break;
        default:
            return parent::getTemplate($name);
            break;
    }
}
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('flujo1', 'flujo1');
        $collection->add('flujo2', 'flujo2');
    }

}
