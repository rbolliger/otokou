<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/otokouTestFunctional.class.php';

$ut = new otkTestUtility();

$t = new lime_test(3, new lime_output_color());


$t->diag('::findByUsername');
$t->cmp_ok(count(Doctrine_Core::getTable('Vehicle')->findByUsername('user_reports')), '===', 20 , '::findByUsername() retrieves all Vehicles for the given user');


$t->diag('::countActiveByUserId()');
$user_id = $ut->getUserId('user_vehicle');
$t->cmp_ok(Doctrine_Core::getTable('Vehicle')->countActiveByUserId($user_id), '===', 1, '->countActiveByUserId() returns the right number of vehicles');


$t->diag('::findArchivedByUserId()');
$user_id = $ut->getUserId('user_gs');
$t->cmp_ok(count(Doctrine_Core::getTable('Vehicle')->findArchivedByUserId($user_id)), '===', 2, '->findArchivedByUserId() returns the right number of vehicles');


