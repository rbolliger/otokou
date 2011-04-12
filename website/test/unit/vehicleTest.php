<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';

$ut = new otokouTestFunctional(new sfBrowser());

$t = new lime_test(4, new lime_output_color());


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

