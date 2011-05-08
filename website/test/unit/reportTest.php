<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/otokouTestFunctional.class.php';

$ut = new otokouTestFunctional(new sfBrowser());

$t = new lime_test(2, new lime_output_color());





$t->diag('getNumVehicles() - One entry');
$vehicles_query = Doctrine_Core::getTable('Vehicle')->createQuery('v')
        ->leftJoin('v.User u')
        ->andWhere('u.username = ?','user_gs')
        ->limit(1);

$report = new Report();
$report->setName('report_test_1');
$report->setUserId($ut->getUserId('user_gs'));
$report->setVehicles($vehicles_query->execute());
$report->save();

$t->cmp_ok($report->getNumVehicles(), '===', 1);

$t->diag('getNumVehicles() - Many entries');
$vehicles_query = Doctrine_Core::getTable('Vehicle')->createQuery('v')
        ->leftJoin('v.User u')
        ->andWhere('u.username = ?','user_gs');

$report = new Report();
$report->setName('report_test_2');
$report->setUserId($ut->getUserId('user_gs'));
$report->setVehicles($vehicles_query->execute());
$report->save();

$t->cmp_ok($report->getNumVehicles(), '===', 2);


