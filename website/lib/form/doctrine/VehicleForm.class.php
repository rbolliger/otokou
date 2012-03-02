<?php

/**
 * Vehicle form.
 *
 * @package    otokou
 * @subpackage form
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class VehicleForm extends BaseVehicleForm {

    public function configure() {

        unset(
                $this['created_at'], $this['updated_at'], $this['slug'], $this['charts_list'], $this['reports_list']
        );

        $this->widgetSchema['name'] = new sfWidgetFormInputText(array(),array('size' => 100));

        $this->widgetSchema['user_id'] = new sfWidgetFormInputHidden();
        $this->validatorSchema['user_id'] = new sfValidatorChoice(array('choices' => array($this->getUserId()), 'required' => true));
    }

    protected function getUserId() {

        return $this->getObject()->get('user_id');
    }

}
