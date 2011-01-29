<?php

/**
 * Charge filter form.
 *
 * @package    otokou
 * @subpackage filter
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ChargeFormWithUserFilter extends BaseChargeFormFilter {

    public function configure() {

        $q = Doctrine_Core::getTable($this->getRelatedModelName('Vehicle'))->createQuery('v')->where('v.user_id = ?', $this->getUserId());

        $this->widgetSchema['vehicle_id'] = new sfWidgetFormDoctrineChoice(array(
                    'model' => $this->getRelatedModelName('Vehicle'),
                    'add_empty' => true,
                    'query' => $q,
                ));

        $this->validatorSchema['vehicle_id'] = new sfValidatorDoctrineChoice(array(
                    'model' => $this->getRelatedModelName('Vehicle'),
                    'query' => $q,
                    'required' => false,
                ));




        $widget = new sfWidgetFormDate(array(
                    'format' => '%day%/%month%/%year%',
                ));

        $this->widgetSchema['date'] = new sfWidgetFormFilterDate(array(
                    'from_date' => new sfWidgetFormJQueryDate(array(
                        'config' => '{}',
                        'image' => '/images/calendar.png',
                        'date_widget' => $widget
                    )),
                    'to_date' => new sfWidgetFormJQueryDate(array(
                        'config' => '{}',
                        'image' => '/images/calendar.png',
                        'date_widget' => $widget
                    )),
                    'with_empty' => false,
                ))
        ;
        
       $this->widgetSchema['kilometers'] = new sfWidgetFormFilterDate(array(
           'from_date' => new sfWidgetFormInput(),
           'to_date' => new sfWidgetFormInput(),
           'with_empty' => false,
           'template' => 'between %from_date% and %to_date%',
           ));
       
       $this->validatorSchema['kilometers'] = new sfValidatorDateRange(array(
           'required' => false,
           'from_date' => new sfValidatorNumber(array('required' => false)),
           'to_date' => new sfValidatorNumber(array('required' => false))
           ));
       
        
        
        
    }

    protected function getUserId() {

        return sfContext::getInstance()->getUser()->getGuarduser()->getId();
    }
    
    public function addKilometersColumnQuery(Doctrine_Query $query, $field, $values) {
       
        parent::addDateQuery($query, $field, $values);
    }
    

}
