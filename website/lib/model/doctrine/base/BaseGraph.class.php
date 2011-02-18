<?php

/**
 * BaseGraph
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $vehicle_display
 * @property integer $user_id
 * @property string $category_display
 * @property date $date_from
 * @property date $date_to
 * @property double $kilometers_from
 * @property double $kilometers_to
 * @property string $range_type
 * @property string $sha
 * @property string $format
 * @property Doctrine_Collection $Vehicles
 * @property Doctrine_Collection $Categories
 * @property sfGuardUser $User
 * @property Doctrine_Collection $GraphVehicles
 * 
 * @method integer             getId()               Returns the current record's "id" value
 * @method string              getVehicleDisplay()   Returns the current record's "vehicle_display" value
 * @method integer             getUserId()           Returns the current record's "user_id" value
 * @method string              getCategoryDisplay()  Returns the current record's "category_display" value
 * @method date                getDateFrom()         Returns the current record's "date_from" value
 * @method date                getDateTo()           Returns the current record's "date_to" value
 * @method double              getKilometersFrom()   Returns the current record's "kilometers_from" value
 * @method double              getKilometersTo()     Returns the current record's "kilometers_to" value
 * @method string              getRangeType()        Returns the current record's "range_type" value
 * @method string              getSha()              Returns the current record's "sha" value
 * @method string              getFormat()           Returns the current record's "format" value
 * @method Doctrine_Collection getVehicles()         Returns the current record's "Vehicles" collection
 * @method Doctrine_Collection getCategories()       Returns the current record's "Categories" collection
 * @method sfGuardUser         getUser()             Returns the current record's "User" value
 * @method Doctrine_Collection getGraphVehicles()    Returns the current record's "GraphVehicles" collection
 * @method Graph               setId()               Sets the current record's "id" value
 * @method Graph               setVehicleDisplay()   Sets the current record's "vehicle_display" value
 * @method Graph               setUserId()           Sets the current record's "user_id" value
 * @method Graph               setCategoryDisplay()  Sets the current record's "category_display" value
 * @method Graph               setDateFrom()         Sets the current record's "date_from" value
 * @method Graph               setDateTo()           Sets the current record's "date_to" value
 * @method Graph               setKilometersFrom()   Sets the current record's "kilometers_from" value
 * @method Graph               setKilometersTo()     Sets the current record's "kilometers_to" value
 * @method Graph               setRangeType()        Sets the current record's "range_type" value
 * @method Graph               setSha()              Sets the current record's "sha" value
 * @method Graph               setFormat()           Sets the current record's "format" value
 * @method Graph               setVehicles()         Sets the current record's "Vehicles" collection
 * @method Graph               setCategories()       Sets the current record's "Categories" collection
 * @method Graph               setUser()             Sets the current record's "User" value
 * @method Graph               setGraphVehicles()    Sets the current record's "GraphVehicles" collection
 * 
 * @package    otokou
 * @subpackage model
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseGraph extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('graph');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('vehicle_display', 'string', 20, array(
             'type' => 'string',
             'length' => 20,
             ));
        $this->hasColumn('user_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('category_display', 'string', 20, array(
             'type' => 'string',
             'length' => 20,
             ));
        $this->hasColumn('date_from', 'date', null, array(
             'type' => 'date',
             ));
        $this->hasColumn('date_to', 'date', null, array(
             'type' => 'date',
             ));
        $this->hasColumn('kilometers_from', 'double', null, array(
             'type' => 'double',
             ));
        $this->hasColumn('kilometers_to', 'double', null, array(
             'type' => 'double',
             ));
        $this->hasColumn('range_type', 'string', 20, array(
             'type' => 'string',
             'length' => 20,
             ));
        $this->hasColumn('sha', 'string', 40, array(
             'type' => 'string',
             'notnull' => true,
             'unique' => true,
             'length' => 40,
             ));
        $this->hasColumn('format', 'string', 5, array(
             'type' => 'string',
             'length' => 5,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Vehicle as Vehicles', array(
             'refClass' => 'GraphVehicle',
             'local' => 'graph_id',
             'foreign' => 'vehicle_id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('Category as Categories', array(
             'refClass' => 'GraphCategory',
             'local' => 'graph_id',
             'foreign' => 'category_id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('sfGuardUser as User', array(
             'local' => 'user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('GraphVehicle as GraphVehicles', array(
             'local' => 'id',
             'foreign' => 'graph_id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}