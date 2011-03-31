<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';

$app_configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
sfContext::createInstance($app_configuration);

$ut = new otokouTestFunctional(new sfBrowser());


$t = new lime_test(55, new lime_output_color());


// ->getQuery()
$t->diag('->getQuery()');

$gb = newGraph(); 
$t->isa_ok($gb->getQuery(), 'Doctrine_Query', '->getQuery() returns a Doctrine_Query object');



// ->getGraphsQueryResults()
// These tests are based on the graph_gb_1 to graph_gb_4 fixtures
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


// ->reloadGraph()
$t->diag('->reloadGraph()');
$g = $gb->getGraph();
$g->setFormat('');
$g->save();
$gb->reloadGraph();
$t->cmp_ok($gb->getGraph(),'===',$g,'->reloadGraph() resets the loaded graph and retrieves the updated graph from the DB');


// -> getGraphFormat()
$t->diag('-> getGraphFormat()');

sfConfig::clear('app_graph_default_format');
$gb = newGraph();

$t->cmp_ok($gb->getGraphFormat(), '==', 'png', 'By default, the pictures format is png');

sfConfig::set('app_graph_default_format', 'jpg');
$g = $gb->getGraph();
$g->setFormat('');
$g->save();
$gb->reloadGraph();
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
$t->cmp_ok($gb->getGraphBasePath(), '==', 'graphs', 'By default, base path is graphs');

sfConfig::set('app_graph_base_path', '/graphs/static');
$t->cmp_ok($gb->getGraphBasePath(), '==', 'graphs/static', 'The user can set the path in app_graph_base_path');

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
$t->cmp_ok($g->buildGraphSource(), '===', true, '->buildGraphSource returns "true" if a GraphSource object can be built');

$g = newGraph(array('user_id' => $ut->getUserId('user_gb_noCars')));
$t->cmp_ok($g->buildGraphSource(), '===', false, '->buildGraphSource returns "false" if a GraphSource object cannot be built');


// ->getParameter()
$t->diag('->getParameter()');

$g = newGraph(array('sdfgsdfgsgd' => 123));
$t->cmp_ok($g->getParameter('sdfgsdfgsgd','aaa'), '===', 123,'->getParameter() returns the requested parameter value if the parameter exists');
$t->cmp_ok($g->getParameter('drasdf'), '===', null,'->getParameter() returns NULL if the parameter is not found and no default value is specified');
$t->cmp_ok($g->getParameter('drasdf','aaa'), '===', 'aaa','->getParameter() returns the specified default value if the parameter is not found');


// ->getGraphSourceData()
$t->diag('->getGraphSourceData()');
$params = array(
    'categories_list' => array($ut->getIdForCategory('Tax'), $ut->getIdForCategory('Fuel')),
    'vehicles_list' => array($ut->getVehicleId('car-gb-1'),$ut->getVehicleId('car-gb-2'),$ut->getVehicleId('car-gb-3')),
);
$g = newGraph($params);
$data = $g->getGraphSourceData('stacked', 'stacked');
$t->cmp_ok(count($data), '<=', 1,'->getGraphSourceData() returns a single data serie when both displays are stacked');

$data = $g->getGraphSourceData('stacked', 'single');
$t->cmp_ok(count($data), '<=', count($params['categories_list']),'->getGraphSourceData() returns at most a number of data series corresponding to the number of categories');


$data = $g->getGraphSourceData('single', 'stacked');
$t->cmp_ok(count($data), '<=', count($params['vehicles_list']),'->getGraphSourceData() returns at most a number of data series corresponding to the number of vehicles');


$data = $g->getGraphSourceData('single', 'single');
$t->cmp_ok(count($data), '<=', count($params['categories_list'])*count($params['vehicles_list']),'->getGraphSourceData() returns at most a number of data series corresponding to the number of vehicles multiplied by the number of categories');
$t->isa_ok($data[0], 'GraphDataSerie', '->getGraphSourceData() returns one or more series of GraphDataSerie');

$t->diag('->getGraphSourceData() when vehicles_list and categories_list are empty');
$params = array(
    'categories_list' => array(),
    'vehicles_list' => array(),
);
$g = newGraph($params);

$v = Doctrine_Core::getTable('Vehicle')->getVehiclesByUserIdQuery($ut->getUserId('user_gb'))->execute();
$c = Doctrine_Core::getTable('Category')->findAll();

$data = $g->getGraphSourceData('stacked', 'stacked');
$t->cmp_ok(count(array_keys($data)), '<=', 1,'->getGraphSourceData() returns a single data serie when both displays are stacked');

$data = $g->getGraphSourceData('stacked', 'single');
$t->cmp_ok(count(array_keys($data)), '<=', count($c),'->getGraphSourceData() returns a number of data series corresponding to the number of categories');


$data = $g->getGraphSourceData('single', 'stacked');
$t->cmp_ok(count(array_keys($data)), '<=', count($v),'->getGraphSourceData() returns a number of data series corresponding to the number of vehicles');


$data = $g->getGraphSourceData('single', 'single');
$t->cmp_ok(count(array_keys($data)), '<=', count($c)*count($v),'->getGraphSourceData() returns a number of data series corresponding to the number of vehicles multiplied by the number of categories');


// ->checkPath()
$t->diag('checkPath()');
$path = '/test';
sfConfig::set('app_graph_base_path',$path);
sfConfig::set('sf_root_dir',realpath(dirname(__FILE__).'/../..'));
sfConfig::set('sf_web_dir',  sfConfig::get('sf_root_dir').'/web');
$fullpath = sfConfig::get('sf_web_dir').'/images'.$path;
$g  = newGraph();
$exist = $g->checkPath($g->getGraphBasePath('system'));
$t->ok(file_exists($fullpath), '->checkPath() checks that a path exists. If not, the path is created');

if ($exist) {
$fs = new sfFilesystem();
$fs->remove($fullpath);
}

$g->checkPath($g->getGraphBasePath('system'),false);
$t->ok(!file_exists(sfConfig::get('sf_web_dir').$path), '->checkPath() accepts a "create" option. If set to false, the path is not created, if not found.');


try
{
  $g->checkPath($g->getGraphBasePath('web'));
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('->checkPath() only accepts system paths');
}

// ->graphSourceIsAvailable()
$t->diag('->graphSourceIsAvailable()');
$g  = newGraph();
$t->cmp_ok($g->graphSourceIsAvailable(), '===', false, 'Graph source file is not available for new graphs');


$fs = new sfFilesystem();
$fs->touch($g->getGraphPath('system'));
$t->cmp_ok($g->graphSourceIsAvailable(), '===', true, 'Graph source file is found, if it exists');
$fs->remove($g->getGraphPath('system'));
$fs->remove($g->getGraphBasePath('system'));

// ->getAttributes()
$t->diag('->getAttributes()');
$attr = array('test' => 'sdfgdg');
$g  = newGraph(array(),array(),$attr);
$t->cmp_ok($g->getAttributes(), '===', $attr, '->getAttributes() returns attributes set via GraphBuilder constructor');

// ->addAttributes()
$t->diag('->addAttributes()');
$attr2 = array('a1234' => '34');
$g->addAttributes($attr2);
$t->cmp_ok($g->getAttributes(), '===', array_merge($attr,$attr2), '->addAttributes() appends new attributes');


// ->setAttributes()
$attr = array('dfsdf' => 'asdfgsdg');
$g->setAttributes($attr);
$t->cmp_ok($g->getAttributes(), '===', $attr, '->setAttributes() resets GraphBuilder attributes and adds new ones');

// ->getParemater()
$t->diag('->getParameter()');
$p = array('test' => 'sdfgdg','asdgdf' => 'asdtert');
$g  = newGraph($p);
$t->cmp_ok($g->getParameter('test'), '===', 'sdfgdg', '->getParameter() returns the paremeter set via the constructor');

// ->addParameters()
$t->diag('->addParameters()');
$g->addParameters(array('a1234' => '34'));
$t->cmp_ok($g->getParameter('test'), '===', 'sdfgdg', '->addParameters() doesn\'t reset existing parameters');
$t->cmp_ok($g->getParameter('a1234'), '===', '34', '->addParameters() appends new parameters to the existing ones');

// ->setParameters()
$t->diag('->setParameters()');
$g->setParameters(array('a1234' => '34'));
$t->cmp_ok($g->getParameter('test'), '===', NULL, '->setParameters() resets existing parameters');
$t->cmp_ok($g->getParameter('a1234'), '===', '34', '->setParameters() creates new parameters');


// ->getLogger()
$t->diag('->getLogger()');
$g = newGraph();
$t->cmp_ok($g->getLogger(), '===', sfContext::getInstance()->getLogger(), 'The default logger is set by the application');

// ->setLogger()
$logger = new sfNoLogger(new sfEventDispatcher());
$g->setLogger($logger);
$t->cmp_ok($g->getLogger(), '===', $logger, '->setLogger() allows to define a custom logger');


// ->generate()
$t->diag('->generate()');
$g = newGraph(array('user_id' => $ut->getUserId('user_gb_noCars')));
$t->cmp_ok($g->generate(), '===', false, 'If the User has no cars, the graph cannot be generated.');

$g = newGraph();
$t->cmp_ok($g->generate(), '===', false, 'Even if the User has cars, nothing is generated by GraphBuilder. This task is left to childrens.');

// ->display()
$t->diag('->display()');
$g = newGraph(array('user_id' => $ut->getUserId('user_gb_noCars')));
$t->like($g->display(), '/Not enough data/', 'If the User has no cars, a message id displayed instead of the graph.');

$g = newGraph();
$t->like($g->display(), '/Not enough data/', 'Even if the User has cars, nothing is generated by GraphBuilder. This task is left to childrens.');


// ->doForceGenerate()
$t->diag('->doForceGenerate()');
$g = newGraph();
sfConfig::clear('app_graphs_force_generate');
$t->cmp_ok($g->doForceGenerate(), '===', false, '->doForceGenerate() returns false by default');

sfConfig::set('app_graphs_force_generate', true);
$t->cmp_ok($g->doForceGenerate(), '===', true, '->doForceGenerate() returns the value set in app_graphs_force_generate');

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
                        'user_id'           => $ut->getUserId('user_gb'),
                        'vehicle_display'   => 'single',
                        'category_display'  => 'stacked',
                        'range_type'        => 'kilometers',
                        'format'            => 'png',
                        'graph_name'        => 'cost_per_km',
                    ),
                    $data);

    return new GraphBuilder(getData($data),$options,$attributes);
}