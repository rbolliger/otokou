<?php

include dirname(__FILE__) . '/../bootstrap/unit.php';

new sfDatabaseManager($configuration);
Doctrine_Core::loadData(sfConfig::get('sf_test_dir') . '/fixtures');

$ut = new otokouTestFunctional(new sfBrowser());


$t = new lime_test(4, new lime_output_color());





// ->getGraphsQueryResults()
$t->diag('->getGraphsQueryResults()');

$data = array('user_id' => $ut->getUserId('user_gb'));
$gb = new GraphBuilder(getData($data));
$t->isa_ok($gb->getGraphsQueryResults(), 'Doctrine_Collection','getGraphsQueryResults() returns a Doctrine_Collection');
$t->is(count($gb->getGraphsQueryResults()),0, 'getGraphsQueryResults() returns nothing if no corresponding object exists in DB');


$data = array(
    'user_id' => $ut->getUserId('user_gb'),
    'categories_list' => array($ut->getIdForCategory('Tax'),$ut->getIdForCategory('Fuel')),
        );
$gb = new GraphBuilder(getData($data));
$t->cmp_ok($gb->getGraphsQueryResults()->count(), '==', 1, 'getGraphsQueryResults() retrieves only entries matching EXACTLY the requested parameters');


// ->getQuery()
$t->diag('->getQuery()');

$gb = new GraphBuilder(array());
$t->isa_ok($gb->getQuery(),'Doctrine_Query','->getQuery() returns a Doctrine_Query object');


//
// ->display()
// ->generate()
// -> getGraphName()
// ->getGraphPath()

// ->getOption()
// ->retrieveOrCreate()
// ->setAttributes()
// ->setOptions()


function getData($data = array()) {

    $fields = Doctrine_Core::getTable('Graph')->getFieldNames();

    $defaults = array_combine($fields, array_fill(0, count($fields), null));

    unset(
            $defaults['created_at'],
            $defaults['updated_at'],
            $defaults['sha'],
            $defaults['id']
    );

    $foreign = array(
        'vehicles_list' => null,
        'categories_list' => null,
    );

    $defaults = array_merge($defaults, $foreign);

    return array_merge($defaults, $data);
}


