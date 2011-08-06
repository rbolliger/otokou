<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';




$username = 'user_gs';
$from = 0;
$to = date('Y-m-d');
$name = '"Total report"';


$argumentsArray = array(
    'username' => $username,
    'vehicles' => 'car-gs-1',
    'name' => $name,
);

$optionsArray = array(
  'kilometers_from' => $from,
  'date_to' => $to,
);


// calling the task
$task = new otokouGenerateReportPdfTask($configuration->getEventDispatcher(), new sfFormatter());


$t = new lime_test(19, new lime_output_color());


$t->diag('1 - Username');
try
{
  $task->run(array_merge($argumentsArray,array('username' => 'eratsdfg'), $optionsArray));
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('A valid username must be defined');
}

// testing arguments
$t->diag('2 - Testing starting and ending points');


try
{
  $task->run($argumentsArray, array_merge($optionsArray,array('date_from' => date('Y-m-d'))));
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('Two starting periods are not accepted');
}

try
{
  $task->run($argumentsArray, array_merge($optionsArray,array('kilometers_to' => 10000)));
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('Two ending periods are not accepted');
}


$t->ok($task->run($argumentsArray,$optionsArray),'The task accepts one starting and one ending points.');

$t->ok($task->run($argumentsArray,array_merge($optionsArray,array('kilometers_from' => null,'date_to' => null,))),'The task accepts empty starting and/or ending points.');




$t->diag('3 - Vehicles list');

$t->diag('3.1 - Single vehicle');

try
{
  $task->run(array_merge($argumentsArray,array('vehicles' => 'sdfgsdfg'), $optionsArray));
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('The user must specify an existing vehicle');
}


try
{
  $task->run(array_merge($argumentsArray,array('vehicles' => 'touran'), $optionsArray));
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('The user must specify a vehicle owned by the requested input user');
}

$t->ok($task->run($argumentsArray,$optionsArray),'When username and vehicle match, the task runs correctly.');


$t->diag('3.2 - Multiple vehicles');

$t->ok($task->run(array_merge($argumentsArray,array('vehicles' => '"car-gs-1,car-gs-2"')),$optionsArray),'Multiple vehicles must be specified as comma-separated values.');
$t->ok($task->run(array_merge($argumentsArray,array('vehicles' => '"car-gs-1, car-gs-2"')),$optionsArray),'", " can be used to separate vehicles.');

try
{
  $task->run(array_merge($argumentsArray,array('vehicles' => 'car-gs-1,sdfgsdfg'), $optionsArray));
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('Only existing vehicles can be specified');
}

try
{
  $task->run(array_merge($argumentsArray,array('vehicles' => 'car-gs-1,touran'), $optionsArray));
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('Only vehicles owned by the input user can be specified');
}


$t->diag('Output file');

$name = 'report_1234567789';
$rq = Doctrine_Core::getTable('Report')->createQuery('r')->where('r.name = ?',$name);

$report = $rq->fetchOne();
$t->ok(!$report,'The pdf report file does not exist before creation');

$task->run(array_merge($argumentsArray,array('name' => $name)),$optionsArray);

$report = $rq->fetchOne();
$t->ok(file_exists($report->getPdfFileFullPath()),'The pdf report file has been created');


$t->diag('No data to build a report');

$name = 'report_no_data';
$rq = Doctrine_Core::getTable('Report')->createQuery('r')->where('r.name = ?',$name);

$report = $rq->fetchOne();
$t->ok(!$report,'The report record does not exist in DB before calling the task');

$status = $task->run(array_merge($argumentsArray,array('name' => $name,'username' => 'user_reports','vehicles' => 'car-reports-1',)),$optionsArray);

$report = $rq->fetchOne();
$t->ok($report,'The report record has been created in the DB by calling the task');

$t->ok(!file_exists($report->getPdfFileFullPath()),'The pdf report has not been created');
$t->cmp_ok($status, '===', false, 'The pdf report has not been generated because no charges are available');

$report = $rq->fetchOne();
$t->ok($report, 'A report record is registered in the DB');
