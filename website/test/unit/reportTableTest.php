<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/otokouTestFunctional.class.php';

$ut = new otkTestUtility();

$t = new lime_test(31, new lime_output_color());


$t->diag('->findCustomReportsByUser()');
$params = array(
    'username' => 'user_gs',
);
$r = Doctrine_Core::getTable('Report')->findCustomReportsByUser($params);

$t->cmp_ok(count($r), '===', 27 , '->findCustomReportsByUser() returns the right number of reports');

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


$t->diag('countReports()');

$t->cmp_ok(Doctrine_Core::getTable('Report')->countReports(), '===', 59, '->countReports() returns the right count when no input query is set.');

$q = Doctrine_Core::getTable('Report')->createQuery('r')
        ->leftJoin('r.User u')
        ->addWhere('u.username = ?','user_gs');

$t->cmp_ok(Doctrine_Core::getTable('Report')->countReports($q), '===', 30, '->countReports() returns the right count when an input query is set.');

