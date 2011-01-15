<?php

/**
 * Charge form.
 *
 * @package    otokou
 * @subpackage form
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ChargeForm extends BaseChargeForm {

    public function configure() {


        unset(
                $this['created_at'], $this['updated_at']
        );


        $this->validatorSchema['kilometers'] = new sfValidatorAnd(array(
                    $this->validatorSchema['kilometers'],
                    new sfValidatorNumber(array('min' => 0)),
                ));

        $this->validatorSchema['amount'] = new sfValidatorAnd(array(
                    $this->validatorSchema['amount'],
                    new sfValidatorNumber(array('min' => 0)),
                ));

        
        $widget = new sfWidgetFormDate(array(
           'format' => '%day%/%month%/%year%',
       ));
        
        $this->widgetSchema['date'] = new sfWidgetFormJQueryDate(array(
                    'config' => '{}',
                    'image'  => '/images/calendar.png',
                    'date_widget' => $widget
                ));


        $this->validatorSchema['quantity'] = new sfValidatorNumber(array('required' => false, 'min' => 0));
    }

}
