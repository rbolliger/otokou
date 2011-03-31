<?php

/**
 * ChartVehicle form base class.
 *
 * @method ChartVehicle getObject() Returns the current form's model object
 *
 * @package    otokou
 * @subpackage form
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseChartVehicleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'chart_id'   => new sfWidgetFormInputHidden(),
      'vehicle_id' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'chart_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('chart_id')), 'empty_value' => $this->getObject()->get('chart_id'), 'required' => false)),
      'vehicle_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('vehicle_id')), 'empty_value' => $this->getObject()->get('vehicle_id'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('chart_vehicle[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ChartVehicle';
  }

}
