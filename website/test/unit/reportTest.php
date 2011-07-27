<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/otokouTestFunctional.class.php';

$ut = new otokouTestFunctional(new sfBrowser());

$t = new lime_test(8, new lime_output_color());





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

$t->cmp_ok($report->getNumVehicles(), '===', 3);


$t->diag('getPdfFileName()');
$pdfname = $report->getHash().'.pdf';
$t->cmp_ok($report->getPdfFileName(), '===', $pdfname, 'Pdf filename is built from hash');

$t->diag('getPdfWebPath()');
$webpath = sfConfig::get('app_report_dir_name');
$t->cmp_ok($report->getPdfWebPath(), '===', $webpath, 'getPdfWebPath() returns the relative url of the pdf files root');

$t->diag('getPdfFileWebPath()');
$filewebpath = $report->getPdfWebPath().'/'.$pdfname;
$t->cmp_ok($report->getPdfFileWebPath(), '===', $filewebpath, 'getPdfFileWebPath() returns the relative url of the pdf file');


$t->diag('getPdfSystemPath()');
$systempath = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $webpath);
$t->cmp_ok($report->getPdfSystemPath(), '===', $systempath, 'getPdfSystemPath() returns the root path of the pdf files');
$t->ok(file_exists(str_replace($pdfname, '', $systempath)),'The folder storing the pdf reports exist');


$t->diag('getPdfFileFullPath()');
$t->cmp_ok($report->getPdfSystemPath().DIRECTORY_SEPARATOR.$report->getPdfFileName(), '===', $report->getPdfFileFullPath(), 'getPdfFileFullPath() returns the full path of the pdf file');