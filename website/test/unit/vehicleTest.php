<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';

$ut = new otokouTestFunctional(new sfBrowser());

$t = new lime_test(8, new lime_output_color());


$v = Doctrine_Core::getTable('Vehicle')->findOneByName('car_gs_1');

// ->getTravelledDistance()
$t->diag('->getTravelledDistance()');
$t->cmp_ok($v->getTravelledDistance(), '==', 400, '->getTravelledDistance() returns the maximum travelled distance of a vehicle');

// ->getOverallCost()
$t->diag('->getOverallCost()');
$t->cmp_ok($v->getOverallCost(), '==', 87, '->getOverallCost() returns the overall cost of a vehicle');

// ->getCostPerKm()
$t->diag('->getCostPerKm()');
$t->cmp_ok($v->getCostPerKm(), '==',87/400 , '->getCostPerKm() returns the cost per kilometer of a vehicle');

// ->getAverageConsumption()
$t->diag('->getAverageConsumption()');
$t->cmp_ok($v->getAverageConsumption(), '==', 60/400*100, '->getAverageConsumption() returns the average consumption of a vehicle');

/**
 *  No Charges
 */
$v = Doctrine_Core::getTable('Vehicle')->findOneByName('car gb_noCharges');

// ->getTravelledDistance()
$t->diag('->getTravelledDistance()');
$t->cmp_ok($v->getTravelledDistance(), '==', 0, '->getTravelledDistance() returns "0" if the vehicles has not charges');

// ->getOverallCost()
$t->diag('->getOverallCost()');
$t->cmp_ok($v->getOverallCost(), '==', 0, '->getOverallCost() returns "0" if the vehicles has not charges');

// ->getCostPerKm()
$t->diag('->getCostPerKm()');
$t->cmp_ok($v->getCostPerKm(), '==',null , '->getCostPerKm() returns "null" if the vehicle has not charges');

// ->getAverageConsumption()
$t->diag('->getAverageConsumption()');
$t->cmp_ok($v->getAverageConsumption(), '==', null, '->getAverageConsumption() returns null" if the vehicle has not charges');
