<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/otokouTestFunctional.class.php';

$ut = new otkTestUtility();

$t = new lime_test(25, new lime_output_color());



$t->diag('findActiveByUsernameAndSortByName()');
$t->can_ok(Doctrine::getTable('Vehicle'), 'findActiveByUsernameAndSortByName', 'Method "findActiveByUsernameAndSortByName" exists');

$params = array('username' => 'user_reports');
$v = Doctrine::getTable('Vehicle')->findActiveByUsernameAndSortByName($params);

$t->cmp_ok(count($v), '===', 20, 'findActiveByUsernameAndSortByName returns the right num ber of elements');

foreach ($v as $key => $vehicle) {
    $name = sprintf('car_reports_%02d', $key+1);
    $msg = 'Vehicle '.$name.' is correctly sorted';
    $t->cmp_ok($vehicle->getName(), '===', $name, $msg);
}

$t->diag('::findByUsernameWithNewReports');
$v = Doctrine_Core::getTable('Vehicle')->findByUsernameWithNewReports('user_reports');
$t->cmp_ok(count($v), '===', 1 , '::findByUsernameWithNewReports() retrieves only Vehicles with new reports');

$t->diag('::findByUsername');
$t->cmp_ok(count(Doctrine_Core::getTable('Vehicle')->findByUsername('user_reports')), '===', 20 , '::findByUsername() retrieves all Vehicles for the given user');


$t->diag('->countActiveByUserId()');
$user_id = $ut->getUserId('user_vehicle');
$t->cmp_ok(Doctrine_Core::getTable('Vehicle')->countActiveByUserId($user_id), '===', 1, '->countActiveByUserId() returns the right number of vehicles');