<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/otokouTestFunctional.class.php';

$ut = new otokouTestFunctional(new sfBrowser('otokou.localhost'));

$pdf_dir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . sfConfig::get('app_report_dir_name');
$chart_dir = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR .'images'. DIRECTORY_SEPARATOR .sfConfig::get('app_charts_base_path');
$fs = new sfFilesystem();
$fs->mkdirs($pdf_dir);
$fs->mkdirs($chart_dir);


$t = new lime_test(20, new lime_output_color());





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


$t->diag('getChartBuilder() - setChartBuilder()');
$report = new Report();
$t->cmp_ok($report->getChartBuilder(),'===',null, 'getChartBuilder() is "null" by default');
$report->setChartBuilder('sfgdfgfg');
$t->cmp_ok($report->getChartBuilder(),'===','sfgdfgfg','setChartBuilder() allows to define a chart builder for generating charts');


$report = new Report();
$report->setName('report_test_2');
$report->setUserId($ut->getUserId('user_gs'));
$report->setVehicles($vehicles_query->execute());
$report->setKilometersFrom(70);
$report->save();

$context = sfContext::createInstance($configuration);

$params = array(
            'range_type' => 'kilometers',
            'chart_name' => 'cost_per_km',
      );

$t->diag('defineChart()');
try
{
  $report->defineChart($context, $params);
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('A chartBuilder must be set in order to call defineCharts()');
}
$report->setChartBuilder('ChartBuilderPChart');

$t->isa_ok($report->defineChart($params), 'ChartBuilderPChart', 'defineChart() returns an instance of chart builder for the given parameters');


$t->diag('defineCharts()');
$t->isa_ok($report->defineCharts(), 'array', 'defineCharts() returns an array containing the definition of all the charts included in a report');



$t->diag('countCharges()');
$t->cmp_ok($report->countCharges(), '===', 15, 'countCharges() returns the number of charges considered to build the report');

$t->diag('hasCharges()');
$t->cmp_ok($report->hasCharges(),'===',true,'hasCharges() returns "true" if the report has at least one related charge');


$t->diag('generatePdf() - data available');
$t->cmp_ok(file_exists($report->getPdfFileFullPath()), '===', false, 'Before calling generatePdf(), the pdf report doesn\'t exist');
$report->generatePdf($context, 'ChartBuilderPChart' , $report->getPdfFileFullPath());
$t->cmp_ok(file_exists($report->getPdfFileFullPath()), '===', true, 'generatePdf() creates a pdf version of the report');



$vehicles_query = Doctrine_Core::getTable('Vehicle')->createQuery('v')
        ->leftJoin('v.User u')
        ->andWhere('u.username = ?','user_reports')
        ->limit(1);

$report = new Report();
$report->setName('report_test_3');
$report->setUserId($ut->getUserId('user_reports'));
$report->setVehicles($vehicles_query->execute());
$report->save();


$t->diag('countCharges()');
$t->cmp_ok($report->countCharges(), '===', 0, 'countCharges() returns the number of charges considered to build the report');

$t->diag('hasCharges()');
$t->cmp_ok($report->hasCharges(),'===',false,'hasCharges() returns "false" if no charges can be loaded for the report');

$t->diag('generatePdf() - no data available');
$status = $report->generatePdf($context, 'ChartBuilderPChart' , $report->getPdfFileFullPath());

$t->cmp_ok($status, '===', false, 'If no charges are available, the pdf report is not generated');

sfToolkit::clearDirectory($pdf_dir);
rmdir($pdf_dir);

sfToolkit::clearDirectory($chart_dir);
rmdir($chart_dir);