<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';



// calling the task
$task = new otokouBatchGenerateReportsPdfTask($configuration->getEventDispatcher(), new sfFormatter());


$t = new lime_test(8, new lime_output_color());




$t->diag('1 - Input values');

$dates = array('abc', '2332asdfas');

foreach ($dates as $date) {

    try {
        $task->run(array(), array('date_from' => $date));
        $t->fail('no code should be executed after throwing an exception');
    } catch (Exception $e) {
        $t->pass('A valid date must be defined. "' . $date . '" is not accepted.');
    }
}


$kilometers = array(-5, 'adsfasfg', '32as');

foreach ($kilometers as $kilometer) {

    try {
        $task->run(array(), array('kilometers_from' => $kilometer));
        $t->fail('no code should be executed after throwing an exception');
    } catch (Exception $e) {
        $t->pass('A valid distance must be defined. "' . $kilometer . '" is not accepted.');
    }
}

$t->diag('2 - Ranges');

try {
    $task->run(array(), array('date_from' => date('Y-m-d'), 'kilometers_from' => 0));
    $t->fail('no code should be executed after throwing an exception');
} catch (Exception $e) {
    $t->pass('Only one between "date_from" and "kilometers_from" can be defined as input.');
}

try {
    $task->run(array(), array('date_to' => date('Y-m-d'), 'kilometers_to' => 0));
    $t->fail('no code should be executed after throwing an exception');
} catch (Exception $e) {
    $t->pass('Only one between "date_to" and "kilometers_to" can be defined as input.');
}


$t->diag('3 - Task execution');

$t->ok($task->run(array(),array('date_to' => date('Y-m-d'), 'kilometers_from' => 0)), 'The task runs correctly');