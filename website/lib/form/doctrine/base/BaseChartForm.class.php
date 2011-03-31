<?php

/**
 * Chart form base class.
 *
 * @method Chart getObject() Returns the current form's model object
 *
 * @package    otokou
 * @subpackage form
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseChartForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'vehicle_display'  => new sfWidgetFormInputText(),
      'user_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'category_display' => new sfWidgetFormInputText(),
      'date_from'        => new sfWidgetFormDate(),
      'date_to'          => new sfWidgetFormDate(),
      'kilometers_from'  => new sfWidgetFormInputText(),
      'kilometers_to'    => new sfWidgetFormInputText(),
      'range_type'       => new sfWidgetFormInputText(),
      'sha'              => new sfWidgetFormInputText(),
      'format'           => new sfWidgetFormInputText(),
      'chart_name'       => new sfWidgetFormInputText(),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
      'vehicles_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Vehicle')),
      'categories_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Category')),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'vehicle_display'  => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'user_id'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'category_display' => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'date_from'        => new sfValidatorDate(array('required' => false)),
      'date_to'          => new sfValidatorDate(array('required' => false)),
      'kilometers_from'  => new sfValidatorPass(array('required' => false)),
      'kilometers_to'    => new sfValidatorPass(array('required' => false)),
      'range_type'       => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'sha'              => new sfValidatorString(array('max_length' => 40)),
      'format'           => new sfValidatorString(array('max_length' => 5, 'required' => false)),
      'chart_name'       => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
      'vehicles_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Vehicle', 'required' => false)),
      'categories_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Category', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Chart', 'column' => array('sha')))
    );

    $this->widgetSchema->setNameFormat('chart[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Chart';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['vehicles_list']))
    {
      $this->setDefault('vehicles_list', $this->object->Vehicles->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['categories_list']))
    {
      $this->setDefault('categories_list', $this->object->Categories->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveVehiclesList($con);
    $this->saveCategoriesList($con);

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

  public function saveCategoriesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['categories_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Categories->getPrimaryKeys();
    $values = $this->getValue('categories_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Categories', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Categories', array_values($link));
    }
  }

}
