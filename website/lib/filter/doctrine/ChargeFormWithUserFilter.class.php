<?php

/**
 * Charge filter form.
 *
 * @package    otokou
 * @subpackage filter
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ChargeFormWithUserFilter extends ChargeFormFilter {

    public function configure() {

        parent::configure();
        
        
        unset($this['user_id']);

        $q = Doctrine_Core::getTable($this->getRelatedModelName('Vehicle'))
                        ->createQuery('v')
                        ->andWhere('v.user_id = ?', $this->getUserId())
                        ->andWhere('v.is_archived = ?', false);
        
               
       
        $this->widgetSchema['vehicle_id'] = new sfWidgetFormDoctrineChoice(array(
                    'model' => $this->getRelatedModelName('Vehicle'),
                    'query' => $q,
                    'multiple' => true,
                    'expanded' => true,
                    'label' =>  'Vehicles',
                ));
        

        $this->validatorSchema['vehicle_id'] = new sfValidatorDoctrineChoice(array(
                    'model' => $this->getRelatedModelName('Vehicle'),
                    'query' => $q,
                    'required' => false,
                    'multiple' => true,
                ));
        
    }

    protected function getUserId() {

        return sfContext::getInstance()->getUser()->getGuarduser()->getId();
    }

}
