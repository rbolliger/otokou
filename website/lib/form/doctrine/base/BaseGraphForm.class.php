<?php

/**
 * Graph form base class.
 *
 * @method Graph getObject() Returns the current form's model object
 *
 * @package    otokou
 * @subpackage form
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseGraphForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'vehicle_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Vehicle'), 'add_empty' => false)),
      'vehicle_display'  => new sfWidgetFormInputText(),
      'user_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'category_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Category'), 'add_empty' => false)),
      'category_display' => new sfWidgetFormInputText(),
      'date_from'        => new sfWidgetFormDate(),
      'date_to'          => new sfWidgetFormDate(),
      'kilometers_from'  => new sfWidgetFormInputText(),
      'kilometers_to'    => new sfWidgetFormInputText(),
      'range_type'       => new sfWidgetFormInputText(),
      'sha'              => new sfWidgetFormInputText(),
      'graph_name'       => new sfWidgetFormInputText(),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'vehicle_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Vehicle'))),
      'vehicle_display'  => new sfValidatorString(array('max_length' => 20)),
      'user_id'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'category_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Category'))),
      'category_display' => new sfValidatorString(array('max_length' => 20)),
      'date_from'        => new sfValidatorDate(array('required' => false)),
      'date_to'          => new sfValidatorDate(array('required' => false)),
      'kilometers_from'  => new sfValidatorPass(array('required' => false)),
      'kilometers_to'    => new sfValidatorPass(array('required' => false)),
      'range_type'       => new sfValidatorString(array('max_length' => 20)),
      'sha'              => new sfValidatorString(array('max_length' => 64)),
      'graph_name'       => new sfValidatorString(array('max_length' => 20)),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('graph[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Graph';
  }

}
