<?php

/**
 * BaseVehicle
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property integer $user_id
 * @property boolean $is_archived
 * @property sfGuardUser $User
 * @property Doctrine_Collection $Charges
 * @property Doctrine_Collection $Charts
 * @property Doctrine_Collection $ChartVehicles
 * @property Doctrine_Collection $Reports
 * @property Doctrine_Collection $ReportVehicles
 * 
 * @method integer             getId()             Returns the current record's "id" value
 * @method string              getName()           Returns the current record's "name" value
 * @method integer             getUserId()         Returns the current record's "user_id" value
 * @method boolean             getIsArchived()     Returns the current record's "is_archived" value
 * @method sfGuardUser         getUser()           Returns the current record's "User" value
 * @method Doctrine_Collection getCharges()        Returns the current record's "Charges" collection
 * @method Doctrine_Collection getCharts()         Returns the current record's "Charts" collection
 * @method Doctrine_Collection getChartVehicles()  Returns the current record's "ChartVehicles" collection
 * @method Doctrine_Collection getReports()        Returns the current record's "Reports" collection
 * @method Doctrine_Collection getReportVehicles() Returns the current record's "ReportVehicles" collection
 * @method Vehicle             setId()             Sets the current record's "id" value
 * @method Vehicle             setName()           Sets the current record's "name" value
 * @method Vehicle             setUserId()         Sets the current record's "user_id" value
 * @method Vehicle             setIsArchived()     Sets the current record's "is_archived" value
 * @method Vehicle             setUser()           Sets the current record's "User" value
 * @method Vehicle             setCharges()        Sets the current record's "Charges" collection
 * @method Vehicle             setCharts()         Sets the current record's "Charts" collection
 * @method Vehicle             setChartVehicles()  Sets the current record's "ChartVehicles" collection
 * @method Vehicle             setReports()        Sets the current record's "Reports" collection
 * @method Vehicle             setReportVehicles() Sets the current record's "ReportVehicles" collection
 * 
 * @package    otokou
 * @subpackage model
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseVehicle extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('vehicle');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 50, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 50,
             ));
        $this->hasColumn('user_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('is_archived', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('sfGuardUser as User', array(
             'local' => 'user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('Charge as Charges', array(
             'local' => 'id',
             'foreign' => 'vehicle_id'));

        $this->hasMany('Chart as Charts', array(
             'refClass' => 'ChartVehicle',
             'local' => 'vehicle_id',
             'foreign' => 'chart_id'));

        $this->hasMany('ChartVehicle as ChartVehicles', array(
             'local' => 'id',
             'foreign' => 'vehicle_id'));

        $this->hasMany('Report as Reports', array(
             'refClass' => 'ReportVehicle',
             'local' => 'vehicle_id',
             'foreign' => 'report_id'));

        $this->hasMany('ReportVehicle as ReportVehicles', array(
             'local' => 'id',
             'foreign' => 'vehicle_id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $sluggable0 = new Doctrine_Template_Sluggable(array(
             ));
        $this->actAs($timestampable0);
        $this->actAs($sluggable0);
    }
}