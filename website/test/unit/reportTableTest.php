<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/otokouTestFunctional.class.php';

$ut = new otkTestUtility();

$t = new lime_test(38, new lime_output_color());



$t->diag('->findByUserAndVehicleSlug()');
$t->can_ok(Doctrine::getTable('Report'), 'findByUserAndVehicleSlug', 'Method "findByUserAndVehicleSlug" exists');

$limit = 7;
$v = Doctrine_Core::getTable('Vehicle')->findOneByName('car_reports_01');
$params = array(
    'username' => 'user_reports',
    'slug'  => $v->getSlug(),
    'limit' => $limit,
    );

$r = Doctrine_Core::getTable('Report')->findByUserAndVehicleSlug($params);

$t->cmp_ok(count($r), '===', $limit , '->findByUserAndVehicleSlug() returns the right number of reports');

foreach ($r as $key => $report) {    
    $name = sprintf('user_reports n.%d', $key);
    $msg = 'Report '.$name.' is correctly sorted';
    $t->cmp_ok($report->getName(), '===', $name, $msg);
}


$t->diag('->findCustomReportsByUser()');
$params = array(
    'username' => 'user_gs',
);
$r = Doctrine_Core::getTable('Report')->findCustomReportsByUser($params);

$t->cmp_ok(count($r), '===', 27 , '->findByUserAndVehicleSlug() returns the right number of reports');

$t->cmp_ok($r[0]->getName(), '===', '0-1000 km - Car gs 1 and Car gs 2', $r[0]->getName().' is correctly sorted');

foreach ($r as $key => $report) {
    
    if($key == 0) {
        continue;
    }
    
    $name = sprintf('user_gs n.%d', $key-1);
    $msg = 'Report '.$name.' is correctly sorted';
    $t->cmp_ok($report->getName(), '===', $name, $msg);
}


$t->diag('->countNewCustomReports()');
$uid = $ut->getUserId('user_gs');

$r = Doctrine_Core::getTable('Report')->countNewCustomReports($uid);

$t->cmp_ok(count($r), '===', 1, '->countNewCustomReports() returns the right number of reports');