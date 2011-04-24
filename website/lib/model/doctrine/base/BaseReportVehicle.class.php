<?php

/**
 * BaseReportVehicle
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $report_id
 * @property integer $vehicle_id
 * @property Report $Report
 * @property Vehicle $Vehicle
 * 
 * @method integer       getReportId()   Returns the current record's "report_id" value
 * @method integer       getVehicleId()  Returns the current record's "vehicle_id" value
 * @method Report        getReport()     Returns the current record's "Report" value
 * @method Vehicle       getVehicle()    Returns the current record's "Vehicle" value
 * @method ReportVehicle setReportId()   Sets the current record's "report_id" value
 * @method ReportVehicle setVehicleId()  Sets the current record's "vehicle_id" value
 * @method ReportVehicle setReport()     Sets the current record's "Report" value
 * @method ReportVehicle setVehicle()    Sets the current record's "Vehicle" value
 * 
 * @package    otokou
 * @subpackage model
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseReportVehicle extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('report_vehicle');
        $this->hasColumn('report_id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));
        $this->hasColumn('vehicle_id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Report', array(
             'local' => 'report_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('Vehicle', array(
             'local' => 'vehicle_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}