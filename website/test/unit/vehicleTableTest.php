<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/otokouTestFunctional.class.php';

//$ut = new otokouTestFunctional(new sfBrowser());

$t = new lime_test(1, new lime_output_color());

/**
 * New vehicle
 */
$t->diag('::findByUsernameWithNewReports');
$v = Doctrine_Core::getTable('Vehicle')->findByUsernameWithNewReports('user_reports');

$t->cmp_ok(count($v), '===', 1 , '::findByUsernameWithNewReports() retrieves only Vehicles with new reports');

