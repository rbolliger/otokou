<?php

/**
 * charge form base class.
 *
 * @method charge getObject() Returns the current form's model object
 *
 * @package    otokou
 * @subpackage form
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasechargeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'vehicle_id'  => new sfWidgetFormInputText(),
      'date'        => new sfWidgetFormDate(),
      'kilometers'  => new sfWidgetFormInputText(),
      'category_id' => new sfWidgetFormInputText(),
      'amount'      => new sfWidgetFormInputText(),
      'comment'     => new sfWidgetFormTextarea(),
      'quantity'    => new sfWidgetFormInputText(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'vehicle_id'  => new sfValidatorInteger(array('required' => false)),
      'date'        => new sfValidatorDate(array('required' => false)),
      'kilometers'  => new sfValidatorPass(array('required' => false)),
      'category_id' => new sfValidatorInteger(array('required' => false)),
      'amount'      => new sfValidatorPass(array('required' => false)),
      'comment'     => new sfValidatorString(array('required' => false)),
      'quantity'    => new sfValidatorPass(array('required' => false)),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('charge[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'charge';
  }

}
