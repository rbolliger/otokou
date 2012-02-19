<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/otokouTestFunctional.class.php';

$ut = new otokouTestFunctional(new sfBrowser());

$t = new lime_test(32, new lime_output_color());

/**
 * New vehicle
 */
$t->diag('*** New vehicle ***');
$v = Doctrine_Core::getTable('Vehicle')->findOneByName('car_gs_1');

// ->getFirstChargeByRange()
$t->diag('->getFirstChargeByRange(\'date\')');
$t->cmp_ok($v->getFirstChargeByRange('date')->getKilometers(), '==', 300, '->getFirstChargeByRange() returns the first charge registered with respect to the date');
$t->cmp_ok($v->getFirstChargeByRange('distance')->getKilometers(), '==', 50, '->getFirstChargeByRange() returns the first charge registered with respect to the distance');

// ->getLastChargeByRange()
$t->diag('->getLastChargeByRange(\'date\')');
$t->cmp_ok($v->getLastChargeByRange('date')->getKilometers(), '==', 400, '->getLastChargeByRange() returns the last charge registered with respect to the date');
$t->cmp_ok($v->getLastChargeByRange('distance')->getKilometers(), '==', 400, '->getLastChargeByRange() returns the last charge registered with respect to the distance');

// ->getTraveledDistance()
$t->diag('->getTraveledDistance()');
$t->cmp_ok($v->getTraveledDistance(), '==', 350, '->getTraveledDistance() returns distance travelled by the vehicle since purchase');

// ->getInitialDistance()
$t->diag('->getInitialDistance()');
$t->cmp_ok($v->getInitialDistance(), '==', 50, '->getInitialDistance() returns the minimum distance travelled and registered with the fist charge');

// ->getFinalDistance()
$t->diag('->getFinalDistance()');
$t->cmp_ok($v->getFinalDistance(), '==', 400, '->getFinalDistance() returns the maximum distance travelled and registered with the last charge');


// ->getOverallCost()
$t->diag('->getOverallCost()');
$t->cmp_ok($v->getOverallCost(), '==', 87, '->getOverallCost() returns the overall cost of a vehicle');

// ->getCostPerKm()
$t->diag('->getCostPerKm()');
$t->cmp_ok($v->getCostPerKm(), '==',87/(400-50) , '->getCostPerKm() returns the cost per kilometer of a vehicle');

// ->getAverageConsumption()
$t->diag('->getAverageConsumption()');
$t->cmp_ok($v->getAverageConsumption(), '==', 60/(400-50)*100, '->getAverageConsumption() returns the average consumption of a vehicle');


/**
 * Second-hand vehicle
 */
$t->diag('*** Second-hand vehicle ***');
$v = Doctrine_Core::getTable('Vehicle')->findOneByName('car_gs_3');

// ->getFirstChargeByRange()
$t->diag('->getFirstChargeByRange(\'date\')');
$t->cmp_ok($v->getFirstChargeByRange('date')->getKilometers(), '==', 79954, '->getFirstChargeByRange() returns the first charge registered with respect to the date');
$t->cmp_ok($v->getFirstChargeByRange('distance')->getKilometers(), '==', 79954, '->getFirstChargeByRange() returns the first charge registered with respect to the distance');


// ->getLastChargeByRange()
$t->diag('->getLastChargeByRange(\'date\')');
$t->cmp_ok($v->getLastChargeByRange('date')->getKilometers(), '==', 81500, '->getLastChargeByRange() returns the last charge registered with respect to the date');
$t->cmp_ok($v->getLastChargeByRange('distance')->getKilometers(), '==', 83000, '->getLastChargeByRange() returns the last charge registered with respect to the distance');

// ->getTraveledDistance()
$t->diag('->getTraveledDistance()');
$t->cmp_ok($v->getTraveledDistance(), '==', 83000-79954, '->getTraveledDistance() returns the distance travelled by the vehicle since purchase');

// ->getInitialDistance()
$t->diag('->getInitialDistance()');
$t->cmp_ok($v->getInitialDistance(), '==', 79954, '->getInitialDistance() returns the minimum distance travelled and registered with the fist charge');

// ->getFinalDistance()
$t->diag('->getFinalDistance()');
$t->cmp_ok($v->getFinalDistance(), '==', 83000, '->getFinalDistance() returns the maximum distance travelled and registered with the last charge');


// ->getOverallCost()
$t->diag('->getOverallCost()');
$t->cmp_ok($v->getOverallCost(), '==', 496, '->getOverallCost() returns the overall cost of a vehicle');

// ->getCostPerKm()
$t->diag('->getCostPerKm()');
$t->cmp_ok($v->getCostPerKm(), '==',496/(83000-79954) , '->getCostPerKm() returns the cost per kilometer of a vehicle');

// ->getAverageConsumption()
$t->diag('->getAverageConsumption()');
$t->cmp_ok($v->getAverageConsumption(), '==', 155/(83000-79954)*100, '->getAverageConsumption() returns the average consumption of a vehicle');


/**
 *  No Charges
 */
$t->diag('*** No available charges ***');
$v = Doctrine_Core::getTable('Vehicle')->findOneByName('car gb_noCharges');

// ->getTraveledDistance()
$t->diag('->getTraveledDistance()');
$t->cmp_ok($v->getTraveledDistance(), '==', 0, '->getTraveledDistance() returns "0" if the vehicles has not charges');

// ->getInitialDistance()
$t->diag('->getInitialDistance()');
$t->cmp_ok($v->getInitialDistance(), '===', null, '->getInitialDistance() returns "null" if the vehicles has not charges');


// ->getFinalDistance()
$t->diag('->getFinalDistance()');
$t->cmp_ok($v->getFinalDistance(), '===', null, '->getFinalDistance() returns "null" if the vehicles has not charges');


// ->getOverallCost()
$t->diag('->getOverallCost()');
$t->cmp_ok($v->getOverallCost(), '==', 0, '->getOverallCost() returns "0" if the vehicles has not charges');

// ->getCostPerKm()
$t->diag('->getCostPerKm()');
$t->cmp_ok($v->getCostPerKm(), '==',null , '->getCostPerKm() returns "null" if the vehicle has not charges');

// ->getAverageConsumption()
$t->diag('->getAverageConsumption()');
$t->cmp_ok($v->getAverageConsumption(), '==', null, '->getAverageConsumption() returns null" if the vehicle has not charges');


/**
 *  General methods
 */

$t->diag('*** General methods ***');

$t->diag('->getOwnReports()');
$v = Doctrine_Core::getTable('Vehicle')->findOneByName('car_reports_01');

$limit = 7;
//$r = $v->getOwnReports($limit);
$r = $v->getReports();

$t->cmp_ok(count($r), '===', $limit , '->getOwnReports() returns the right number of reports');

foreach ($r as $key => $report) {    
    $name = sprintf('user_reports n.%d', $key);
    $msg = 'Report '.$name.' is correctly sorted';
    $t->cmp_ok($report->getName(), '===', $name, $msg);
}