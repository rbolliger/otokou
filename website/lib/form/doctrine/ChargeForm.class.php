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

        $this->widgetSchema['user_id'] = new sfWidgetFormInputHidden();
        $this->validatorSchema['user_id'] = new sfValidatorChoice(array('choices' => array($this->getUserId()), 'required' => true));


        $this->validatorSchema['kilometers'] = new sfValidatorAnd(array(
                    $this->validatorSchema['kilometers'],
                    new sfValidatorNumber(array('min' => 0)),
                ));

        $this->validatorSchema['amount'] = new sfValidatorAnd(array(
                    $this->validatorSchema['amount'],
                    new sfValidatorNumber(array('min' => 0)),
                ));


        $years = range(1970, date('Y') + 2);
        
        $widget = new sfWidgetFormDate(array(
                    'format' => '%day%/%month%/%year%',
                    'years'  => array_combine($years, $years),
                ));

        $this->widgetSchema['date'] = new sfWidgetFormJQueryDate(array(
                    'config' => '{}',
                    'image' => '/images/calendar.png',
                    'date_widget' => $widget
                ));


        $q = Doctrine_Core::getTable($this->getRelatedModelName('Vehicle'))
                ->createQuery('v')
                ->andWhere('v.user_id = ?', $this->getObject()->getUserId())
                ->andWhere('v.is_archived = ?', false);

        $this->widgetSchema['vehicle_id'] = new sfWidgetFormDoctrineChoice(array(
                    'model' => $this->getRelatedModelName('Vehicle'),
                    'add_empty' => false,
                    'query' => $q,
                ));

        $this->validatorSchema['vehicle_id'] = new sfValidatorDoctrineChoice(array(
                    'model' => $this->getRelatedModelName('Vehicle'),
                    'query' => $q,
                ));


        $this->validatorSchema['quantity'] = new sfValidatorNumber(array('required' => false, 'min' => 0));

        $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkQuantity'))));
    }

    public function checkQuantity($validator, $values) {

        $category = 'Fuel';
        $id = Doctrine_Core::getTable('Category')->findOneByName($category)->getId();


        if ($values['category_id'] == $id && !$values['quantity']) {
            $error = new sfValidatorError($validator, "A quantity must be specified for category $category.");

            // throw an error bound to the password field
            throw new sfValidatorErrorSchema($validator, array('quantity' => $error));
        } elseif ($values['category_id'] !== $id && $values['quantity']) {
            $error = new sfValidatorError($validator, "A quantity can be specified only for category $category.");

            // throw an error bound to the password field
            throw new sfValidatorErrorSchema($validator, array('quantity' => $error));
        }

        // password is correct, return the clean values
        return $values;
    }

    protected function getUserId() {

        return $this->getObject()->get('user_id');
    }

}
