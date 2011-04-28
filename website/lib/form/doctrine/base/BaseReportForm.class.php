<?php

/**
 * Report form base class.
 *
 * @method Report getObject() Returns the current form's model object
 *
 * @package    otokou
 * @subpackage form
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseReportForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'user_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'name'            => new sfWidgetFormInputText(),
      'date_from'       => new sfWidgetFormDate(),
      'date_to'         => new sfWidgetFormDate(),
      'kilometers_from' => new sfWidgetFormInputText(),
      'kilometers_to'   => new sfWidgetFormInputText(),
      'is_new'          => new sfWidgetFormInputText(),
      'num_vehicles'    => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
      'slug'            => new sfWidgetFormInputText(),
      'sha'             => new sfWidgetFormInputText(),
      'vehicles_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Vehicle')),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'name'            => new sfValidatorPass(),
      'date_from'       => new sfValidatorDate(array('required' => false)),
      'date_to'         => new sfValidatorDate(array('required' => false)),
      'kilometers_from' => new sfValidatorPass(array('required' => false)),
      'kilometers_to'   => new sfValidatorPass(array('required' => false)),
      'is_new'          => new sfValidatorPass(array('required' => false)),
      'num_vehicles'    => new sfValidatorInteger(),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
      'slug'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'sha'             => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'vehicles_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Vehicle', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'Report', 'column' => array('slug'))),
        new sfValidatorDoctrineUnique(array('model' => 'Report', 'column' => array('sha'))),
      ))
    );

    $this->widgetSchema->setNameFormat('report[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Report';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['vehicles_list']))
    {
      $this->setDefault('vehicles_list', $this->object->Vehicles->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveVehiclesList($con);

    parent::doSave($con);
  }

  public function saveVehiclesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['vehicles_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Vehicles->getPrimaryKeys();
    $values = $this->getValue('vehicles_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Vehicles', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Vehicles', array_values($link));
    }
  }

}
