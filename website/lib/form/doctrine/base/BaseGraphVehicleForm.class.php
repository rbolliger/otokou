<?php

/**
 * GraphVehicle form base class.
 *
 * @method GraphVehicle getObject() Returns the current form's model object
 *
 * @package    otokou
 * @subpackage form
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseGraphVehicleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'graph_id'   => new sfWidgetFormInputHidden(),
      'vehicle_id' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'graph_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('graph_id')), 'empty_value' => $this->getObject()->get('graph_id'), 'required' => false)),
      'vehicle_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('vehicle_id')), 'empty_value' => $this->getObject()->get('vehicle_id'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('graph_vehicle[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'GraphVehicle';
  }

}
