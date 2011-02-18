<?php

/**
 * Graph filter form base class.
 *
 * @package    otokou
 * @subpackage filter
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseGraphFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'vehicle_display'  => new sfWidgetFormFilterInput(),
      'user_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'category_display' => new sfWidgetFormFilterInput(),
      'date_from'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'date_to'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'kilometers_from'  => new sfWidgetFormFilterInput(),
      'kilometers_to'    => new sfWidgetFormFilterInput(),
      'range_type'       => new sfWidgetFormFilterInput(),
      'sha'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'format'           => new sfWidgetFormFilterInput(),
      'created_at'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'vehicles_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Vehicle')),
      'categories_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Category')),
    ));

    $this->setValidators(array(
      'vehicle_display'  => new sfValidatorPass(array('required' => false)),
      'user_id'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'category_display' => new sfValidatorPass(array('required' => false)),
      'date_from'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'date_to'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'kilometers_from'  => new sfValidatorPass(array('required' => false)),
      'kilometers_to'    => new sfValidatorPass(array('required' => false)),
      'range_type'       => new sfValidatorPass(array('required' => false)),
      'sha'              => new sfValidatorPass(array('required' => false)),
      'format'           => new sfValidatorPass(array('required' => false)),
      'created_at'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'vehicles_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Vehicle', 'required' => false)),
      'categories_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Category', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('graph_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addVehiclesListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.GraphVehicle GraphVehicle')
      ->andWhereIn('GraphVehicle.vehicle_id', $values)
    ;
  }

  public function addCategoriesListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.GraphCategory GraphCategory')
      ->andWhereIn('GraphCategory.category_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Graph';
  }

  public function getFields()
  {
    return array(
      'id'               => 'Number',
      'vehicle_display'  => 'Text',
      'user_id'          => 'ForeignKey',
      'category_display' => 'Text',
      'date_from'        => 'Date',
      'date_to'          => 'Date',
      'kilometers_from'  => 'Text',
      'kilometers_to'    => 'Text',
      'range_type'       => 'Text',
      'sha'              => 'Text',
      'format'           => 'Text',
      'created_at'       => 'Date',
      'updated_at'       => 'Date',
      'vehicles_list'    => 'ManyKey',
      'categories_list'  => 'ManyKey',
    );
  }
}
