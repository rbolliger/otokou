<?php

include dirname(__FILE__) . '/../bootstrap/unit.php';

new sfDatabaseManager($configuration);
Doctrine_Core::loadData(sfConfig::get('sf_test_dir') . '/fixtures');

$ut = new otokouTestFunctional(new sfBrowser());


$t = new lime_test(32, new lime_output_color());


// ->getQuery()
$t->diag('->getQuery()');

$gb = newGraph(array());
$t->isa_ok($gb->getQuery(), 'Doctrine_Query', '->getQuery() returns a Doctrine_Query object');



// ->getGraphsQueryResults()
$t->diag('->getGraphsQueryResults()');


$gb = newGraph();
$qr = $gb->getGraphsQueryResults();
$t->isa_ok($qr, 'Doctrine_Collection', 'getGraphsQueryResults() returns A Doctrine_Collection');
$t->is(count($qr), 0, 'getGraphsQueryResults() returns nothing if no corresponding object exists in DB');


$data = array(
    'categories_list' => array($ut->getIdForCategory('Tax'), $ut->getIdForCategory('Fuel')),
);
$gb = newGraph($data);
$t->isa_ok($gb->getGraphsQueryResults(), 'Doctrine_Collection', 'getGraphsQueryResults() returns a Doctrine_Collection object if the requested object is found in DB');
$t->cmp_ok($gb->getGraphsQueryResults()->count(), '==', 1, 'getGraphsQueryResults() retrieves only entries matching EXACTLY the requested parameters');



// ->retrieveOrCreate()
$t->diag('->retrieveOrCreate()');

$data = array(
    'categories_list' => array($ut->getIdForCategory('Tax'), $ut->getIdForCategory('Fuel')),
);
$gb = newGraph($data);
$g1 = $gb->retrieveOrCreate();
$g2 = $gb->getGraphsQueryResults();
$t->isa_ok($g1, 'Graph', '->retriveOrCreate() returns a Graph object when the element is found in the DB');
$t->is($g1, $g2[0], '->retriveOrCreate() returns a graph found in DB, if available');


$data = array(
    'categories_list' => array($ut->getIdForCategory('Tax'), $ut->getIdForCategory('Fuel')),
    'kilometers_from' => 123,
);
$gb = newGraph($data);
$g1 = $gb->getGraphsQueryResults();
$g2 = $gb->retrieveOrCreate();
$t->isa_ok($g2, 'Graph', '->retriveOrCreate() returns a new Graph object when nohing is in DB');
$t->cmp_ok($g1->count(), '==', 0, '->retriveOrCreate() returns a new Graph if nothing exists in the DB');
$g3 = $gb->getGraphsQueryResults();
$t->cmp_ok($g3->count(), '==', 1, 'the newly created graph can be retrieved from the DB');
$t->is($g2, $g3[0], '->retriveOrCreate() saves the new Graph in the DB');


// -> getGraphFormat()
$t->diag('-> getGraphFormat()');

sfConfig::clear('app_graph_default_format');
$gb = newGraph();

$t->cmp_ok($gb->getGraphFormat(), '==', 'png', 'By default, the pictures format is png');

sfConfig::set('app_graph_default_format', 'jpg');
$t->cmp_ok($gb->getGraphFormat(), '==', 'jpg', 'The user can set a default format in app_graph_default_format');

$data = array('format' => 'png');
$gb = newGraph($data);

$t->cmp_ok($gb->getGraphFormat(), '==', 'png', 'The format can be specified for each graph individually');


// ->getGraphName()
$t->diag('->getGraphName()');
$g = newGraph();
$name = $g->getGraph()->getSha() . '.' . $g->getGraphFormat();
$t->cmp_ok($name, '==', $g->getGraphName(), 'The name of the graph is built from the sha key and the format');



// ->getGraphBasePath()
$t->diag('->getGraphBasePath()');

sfConfig::clear('app_graph_base_path');
$gb = newGraph();
$t->cmp_ok($gb->getGraphBasePath(), '==', '/images/graphs', 'By default, base path is /images/graphs');

sfConfig::set('app_graph_base_path', '/images/graphs/static');
$t->cmp_ok($gb->getGraphBasePath(), '==', '/images/graphs/static', 'The user can set the path in app_graph_base_path');

$options = array('base_path' => '/images/static');
$gb = newGraph(array(),$options);
$t->cmp_ok($gb->getGraphBasePath(), '==', '/images/static', 'The base path can be set for each GraphBuilder instance individually');

$t->cmp_ok($gb->getGraphBasePath('web'), '==', '/images/static', 'The "web" option returns a relative url');

sfConfig::set('sf_web_dir',realpath(dirname(__FILE__).'/../../web'));
$t->cmp_ok($gb->getGraphBasePath('system'), '==', sfConfig::get('sf_web_dir').'/images/static', 'The "system" option returns an absolute system path');

try
{
  $gb->getGraphBasePath('sdgdfgxdf');
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('Only "web" and "system" are possible types');
}


$t->diag('-> getGraphPath()');
$g = newGraph();
$path = $g->getGraphBasePath() . '/' . $g->getGraphName();
$t->cmp_ok($path, '==', $g->getGraphPath(), 'The graph is built from the base path and the graph name');


// ->buildGraphSource()
$t->diag('->buildGraphSource()');
$g = newGraph();
$t->isa_ok($g->buildGraphSource(), 'GraphSource', '->buildGraphSource returns a GraphSource object');


// ->getParam()
$t->diag('->getParam()');

$g = newGraph(array('sdfgsdfgsgd' => 123));
$t->cmp_ok($g->getParam('sdfgsdfgsgd','aaa'), '===', 123,'->getParam() returns the requested parameter value if the parameter exists');
$t->cmp_ok($g->getParam('drasdf'), '===', null,'->getParam() returns NULL if the parameter is not found and no default value is specified');
$t->cmp_ok($g->getParam('drasdf','aaa'), '===', 'aaa','->getParam() returns the specified default value if the parameter is not found');


// ->getGraphSourceData()
$t->diag('->getGraphSourceData()');
$params = array(
    'categories_list' => array($ut->getIdForCategory('Tax'), $ut->getIdForCategory('Fuel')),
    'vehicles_list' => array($ut->getVehicleId('car-gb-1'),$ut->getVehicleId('car-gb-2'),$ut->getVehicleId('car-gb-3')),
);
$g = newGraph($params);
$data = $g->getGraphSourceData('stacked', 'stacked');
$t->cmp_ok(count(array_keys($data)), '==', 1,'->getGraphSourceData() returns a single data serie when both displays are stacked');

$data = $g->getGraphSourceData('stacked', 'single');
$t->cmp_ok(count(array_keys($data)), '==', 2,'->getGraphSourceData() returns a number of data series corresponding to the number of categories');


$data = $g->getGraphSourceData('single', 'stacked');
$t->cmp_ok(count(array_keys($data)), '==', 3,'->getGraphSourceData() returns a number of data series corresponding to the number of vehicles');


$data = $g->getGraphSourceData('single', 'single');
$t->cmp_ok(count(array_keys($data)), '==', 6,'->getGraphSourceData() returns a number of data series corresponding to the number of vehicles multiplied by the number of categories');
$t->isa_ok($data[0], 'Doctrine_Collection', '->getGraphSourceData() returns one or more series of Dcotrine_Collection');


// ->checkPath()
$t->diag('checkPath()');
$path = '/images/test';
sfConfig::set('app_graph_base_path',$path);
sfConfig::set('sf_web_dir',realpath(dirname(__FILE__).'/../../web'));
$g  = newGraph();
$g->checkPath($g->getGraphBasePath());
$t->ok(file_exists(sfConfig::get('sf_web_dir').$path), '->checkPath() checks that a path exists and created it if not');

$fs = new sfFilesystem();
$fs->remove(sfConfig::get('sf_web_dir').$path);

//
//
// ->display()
// ->generate()
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

function newGraph($data = array(),$options = array(),$attributes = array()) {

    $ut = new otokouTestFunctional(new sfBrowser());

    $data = array_merge(
                    array(
                        'user_id' => $ut->getUserId('user_gb'),
                    ),
                    $data);
    return new GraphBuilder(getData($data),$options,$attributes);
}