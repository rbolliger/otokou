<?php

/**
 * BaseReport
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $user_id
 * @property date $date_from
 * @property date $date_to
 * @property double $kilometers_from
 * @property double $kilometers_to
 * @property string $sha
 * @property Doctrine_Collection $Vehicles
 * @property sfGuardUser $User
 * @property Doctrine_Collection $ReportVehicles
 * 
 * @method integer             getId()              Returns the current record's "id" value
 * @method integer             getUserId()          Returns the current record's "user_id" value
 * @method date                getDateFrom()        Returns the current record's "date_from" value
 * @method date                getDateTo()          Returns the current record's "date_to" value
 * @method double              getKilometersFrom()  Returns the current record's "kilometers_from" value
 * @method double              getKilometersTo()    Returns the current record's "kilometers_to" value
 * @method string              getSha()             Returns the current record's "sha" value
 * @method Doctrine_Collection getVehicles()        Returns the current record's "Vehicles" collection
 * @method sfGuardUser         getUser()            Returns the current record's "User" value
 * @method Doctrine_Collection getReportVehicles()  Returns the current record's "ReportVehicles" collection
 * @method Report              setId()              Sets the current record's "id" value
 * @method Report              setUserId()          Sets the current record's "user_id" value
 * @method Report              setDateFrom()        Sets the current record's "date_from" value
 * @method Report              setDateTo()          Sets the current record's "date_to" value
 * @method Report              setKilometersFrom()  Sets the current record's "kilometers_from" value
 * @method Report              setKilometersTo()    Sets the current record's "kilometers_to" value
 * @method Report              setSha()             Sets the current record's "sha" value
 * @method Report              setVehicles()        Sets the current record's "Vehicles" collection
 * @method Report              setUser()            Sets the current record's "User" value
 * @method Report              setReportVehicles()  Sets the current record's "ReportVehicles" collection
 * 
 * @package    otokou
 * @subpackage model
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseReport extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('report');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('user_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
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
        $this->hasColumn('sha', 'string', 40, array(
             'type' => 'string',
             'notnull' => true,
             'unique' => true,
             'length' => 40,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Vehicle as Vehicles', array(
             'refClass' => 'ReportVehicle',
             'local' => 'report_id',
             'foreign' => 'vehicle_id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('sfGuardUser as User', array(
             'local' => 'user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('ReportVehicle as ReportVehicles', array(
             'local' => 'id',
             'foreign' => 'report_id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}