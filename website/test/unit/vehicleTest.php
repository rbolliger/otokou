<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/otokouTestFunctional.class.php';

$ut = new otokouTestFunctional(new sfBrowser());

$t = new lime_test(22, new lime_output_color());

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
$t->cmp_ok($v->getTraveledDistance(), '==', 400, '->getTraveledDistance() returns the maximum travelled distance of a vehicle');

// ->getInitialDistance()
$t->diag('->getInitialDistance()');
$t->cmp_ok($v->getInitialDistance(), '==', 50, '->getInitialDistance() returns the minimum distance travelled and registered with the fist charge');

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
$t->cmp_ok($v->getTraveledDistance(), '==', 83000, '->getTraveledDistance() returns the maximum travelled distance of a vehicle');

// ->getInitialDistance()
$t->diag('->getInitialDistance()');
$t->cmp_ok($v->getInitialDistance(), '==', 79954, '->getInitialDistance() returns the minimum distance travelled and registered with the fist charge');

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

// ->getOverallCost()
$t->diag('->getOverallCost()');
$t->cmp_ok($v->getOverallCost(), '==', 0, '->getOverallCost() returns "0" if the vehicles has not charges');

// ->getCostPerKm()
$t->diag('->getCostPerKm()');
$t->cmp_ok($v->getCostPerKm(), '==',null , '->getCostPerKm() returns "null" if the vehicle has not charges');

// ->getAverageConsumption()
$t->diag('->getAverageConsumption()');
$t->cmp_ok($v->getAverageConsumption(), '==', null, '->getAverageConsumption() returns null" if the vehicle has not charges');
