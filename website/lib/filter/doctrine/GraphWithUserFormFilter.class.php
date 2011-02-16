<?php

/**
 * Graph filter form.
 *
 * @package    otokou
 * @subpackage filter
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class GraphWithUserFormFilter extends GraphFormFilter {

    public function configure() {

        parent::configure();

        unset($this['user_id']);

        $q = Doctrine_Core::getTable($this->getRelatedModelName('Vehicle'))
                        ->createQuery('v')
                        ->andWhere('v.user_id = ?', $this->getUserId());


        $this->widgetSchema['vehicle_id']->setOption('query', $q);
        $this->validatorSchema['vehicle_id']->setOption('query', $q);



        


    }

    protected function getUserId() {

        return sfContext::getInstance()->getUser()->getGuarduser()->getId();
    }

}
