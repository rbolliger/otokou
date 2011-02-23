<?php

include dirname(__FILE__) . '/../bootstrap/unit.php';

new sfDatabaseManager($configuration);
Doctrine_Core::loadData(sfConfig::get('sf_test_dir') . '/fixtures');

$ut = new otokouTestFunctional(new sfBrowser());


$t = new lime_test(14, new lime_output_color());


// ->getQuery()
$t->diag('->getQuery()');

$gb = new GraphBuilder(array());
$t->isa_ok($gb->getQuery(),'Doctrine_Query','->getQuery() returns a Doctrine_Query object');



// ->getGraphsQueryResults()
$t->diag('->getGraphsQueryResults()');

$data = array('user_id' => $ut->getUserId('user_gb'));
$gb = new GraphBuilder(getData($data));
$qr = $gb->getGraphsQueryResults();
$t->isa_ok($qr, 'Doctrine_Collection','getGraphsQueryResults() returns A Doctrine_Collection');
$t->is(count($qr),0, 'getGraphsQueryResults() returns nothing if no corresponding object exists in DB');


$data = array(
    'user_id' => $ut->getUserId('user_gb'),
    'categories_list' => array($ut->getIdForCategory('Tax'),$ut->getIdForCategory('Fuel')),
        );
$gb = new GraphBuilder(getData($data));
$t->isa_ok($gb->getGraphsQueryResults(), 'Doctrine_Collection','getGraphsQueryResults() returns a Doctrine_Collection object if the requested object is found in DB');
$t->cmp_ok($gb->getGraphsQueryResults()->count(), '==', 1, 'getGraphsQueryResults() retrieves only entries matching EXACTLY the requested parameters');



// ->retrieveOrCreate()
$t->diag('->retrieveOrCreate()');

$data = array(
    'user_id' => $ut->getUserId('user_gb'),
    'categories_list' => array($ut->getIdForCategory('Tax'),$ut->getIdForCategory('Fuel')),
        );
$gb = new GraphBuilder(getData($data));
$g1 = $gb->retrieveOrCreate();
$g2 = $gb->getGraphsQueryResults();
$t->isa_ok($g1, 'Graph', '->retriveOrCreate() returns a Graph object when the element is found in the DB');
$t->is($g1, $g2[0],'->retriveOrCreate() returns a graph found in DB, if available');


$data = array(
    'user_id' => $ut->getUserId('user_gb'),
    'categories_list' => array($ut->getIdForCategory('Tax'),$ut->getIdForCategory('Fuel')),
    'kilometers_from' => 123,
        );
$gb = new GraphBuilder(getData($data));
$g1 = $gb->getGraphsQueryResults();
$g2 = $gb->retrieveOrCreate();
$t->isa_ok($g2, 'Graph', '->retriveOrCreate() returns a new Graph object when nohing is in DB');
$t->cmp_ok($g1->count(), '==', 0,'->retriveOrCreate() returns a new Graph if nothing exists in the DB');
$g3 = $gb->getGraphsQueryResults();
$t->cmp_ok($g3->count(), '==', 1,'the newly created graph can be retrieved from the DB');
$t->is($g2, $g3[0],'->retriveOrCreate() saves the new Graph in the DB');


// -> getGraphFormat()
$t->diag('-> getGraphFormat()');

sfConfig::clear('app_graph_default_format');
$data = array('user_id' => $ut->getUserId('user_gb'));
$gb = new GraphBuilder(getData($data));

$t->cmp_ok($gb->getGraphFormat(), '==', 'png','By default, the pictures format is png');

sfConfig::set('app_graph_default_format', 'jpg');
$t->cmp_ok($gb->getGraphFormat(), '==', 'jpg','The user can set a default format in app_graph_default_format');

$data = array('user_id' => $ut->getUserId('user_gb'),'format' => 'png');
$gb = new GraphBuilder(getData($data));

$t->cmp_ok($gb->getGraphFormat(), '==', 'png','The format can be specified for each graph individually');

// ->display()
// ->generate()
// ->getGraphName()
// ->getGraphPath()
// ->addParamaters() unsets $this->graph

// ->getOption()
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


