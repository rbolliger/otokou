<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/otokouTestFunctional.class.php';

//$ut = new otokouTestFunctional(new sfBrowser());

$t = new lime_test(9, new lime_output_color());



$t->diag('findByUserAndVehicleSlug()');
$t->can_ok(Doctrine::getTable('Report'), 'findByUserAndVehicleSlug', 'Method "findByUserAndVehicleSlug" exists');

$limit = 7;
$v = Doctrine_Core::getTable('Vehicle')->findOneByName('car_reports_01');
$params = array(
    'username' => 'user_reports',
    'slug'  => $v->getSlug(),
    'limit' => $limit,
    );


//$r = $v->getOwnReports($limit);
$r = Doctrine_Core::getTable('Report')->findByUserAndVehicleSlug($params);

$t->cmp_ok(count($r), '===', $limit , '->getOwnReports() returns the right number of reports');

foreach ($r as $key => $report) {    
    $name = sprintf('user_reports n.%d', $key);
    $msg = 'Report '.$name.' is correctly sorted';
    $t->cmp_ok($report->getName(), '===', $name, $msg);
}