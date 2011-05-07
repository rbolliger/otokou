<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';

$app_configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
sfContext::createInstance($app_configuration);

$ut = new otokouTestFunctional(new sfBrowser());


$t = new lime_test(58, new lime_output_color());


// ->getQuery()
$t->diag('->getQuery()');

$gb = newChart(); 
$t->isa_ok($gb->getQuery(), 'Doctrine_Query', '->getQuery() returns a Doctrine_Query object');



// ->getChartsQueryResults()
// These tests are based on the chart_gb_1 to chart_gb_4 fixtures
$t->diag('->getChartsQueryResults()');

$gb = newChart();
$qr = $gb->getChartsQueryResults();
$t->isa_ok($qr, 'Doctrine_Collection', 'getChartsQueryResults() returns A Doctrine_Collection');
$t->is(count($qr), 0, 'getChartsQueryResults() returns nothing if no corresponding object exists in DB');


$data = array(
    'categories_list' => array($ut->getIdForCategory('Tax'), $ut->getIdForCategory('Fuel')),
);
$gb = newChart($data);
$t->isa_ok($gb->getChartsQueryResults(), 'Doctrine_Collection', 'getChartsQueryResults() returns a Doctrine_Collection object if the requested object is found in DB');
$t->cmp_ok($gb->getChartsQueryResults()->count(), '==', 1, 'getChartsQueryResults() retrieves only entries matching EXACTLY the requested parameters');


// ->retrieveOrCreate()
$t->diag('->retrieveOrCreate()');

$data = array(
    'categories_list' => array($ut->getIdForCategory('Tax'), $ut->getIdForCategory('Fuel')),
);
$gb = newChart($data);
$g1 = $gb->retrieveOrCreate();
$g2 = $gb->getChartsQueryResults();
$t->isa_ok($g1, 'Chart', '->retriveOrCreate() returns a Chart object when the element is found in the DB');
$t->is($g1, $g2[0], '->retriveOrCreate() returns a chart found in DB, if available');


$data = array(
    'categories_list' => array($ut->getIdForCategory('Tax'), $ut->getIdForCategory('Fuel')),
    'kilometers_from' => 123,
);
$gb = newChart($data);
$g1 = $gb->getChartsQueryResults();
$g2 = $gb->retrieveOrCreate();
$t->isa_ok($g2, 'Chart', '->retriveOrCreate() returns a new Chart object when nohing is in DB');
$t->cmp_ok($g1->count(), '==', 0, '->retriveOrCreate() returns a new Chart if nothing exists in the DB');
$g3 = $gb->getChartsQueryResults();
$t->cmp_ok($g3->count(), '==', 1, 'the newly created chart can be retrieved from the DB');
$t->is($g2, $g3[0], '->retriveOrCreate() saves the new Chart in the DB');


// ->reloadChart()
$t->diag('->reloadChart()');
$g = $gb->getChart();
$g->setFormat('');
$g->save();
$gb->reloadChart();
$t->cmp_ok($gb->getChart(),'===',$g,'->reloadChart() resets the loaded chart and retrieves the updated chart from the DB');


// -> getChartFormat()
$t->diag('-> getChartFormat()');

sfConfig::clear('app_chart_default_format');
$gb = newChart();

$t->cmp_ok($gb->getChartFormat(), '==', 'png', 'By default, the pictures format is png');

sfConfig::set('app_chart_default_format', 'jpg');
$g = $gb->getChart();
$g->setFormat('');
$g->save();
$gb->reloadChart();
$t->cmp_ok($gb->getChartFormat(), '==', 'jpg', 'The user can set a default format in app_chart_default_format');

$data = array('format' => 'png');
$gb = newChart($data);

$t->cmp_ok($gb->getChartFormat(), '==', 'png', 'The format can be specified for each chart individually');


// ->getChartName()
$t->diag('->getChartName()');
$g = newChart();
$name = $g->getChart()->getHash() . '.' . $g->getChartFormat();
$t->cmp_ok($name, '==', $g->getChartName(), 'The name of the chart is built from the sha key and the format');



// ->getChartBasePath()
$t->diag('->getChartBasePath()');

sfConfig::clear('app_chart_base_path');
$gb = newChart();
$t->cmp_ok($gb->getChartBasePath(), '==', 'charts', 'By default, base path is charts');

sfConfig::set('app_chart_base_path', '/charts/static');
$t->cmp_ok($gb->getChartBasePath(), '==', 'charts/static', 'The user can set the path in app_chart_base_path');

try
{
  $gb->getChartBasePath('sdgdfgxdf');
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('Only "web" and "system" are possible types');
}


$t->diag('-> getChartPath()');
$g = newChart();
$path = $g->getChartBasePath() . '/' . $g->getChartName();
$t->cmp_ok($path, '==', $g->getChartPath(), 'The chart is built from the base path and the chart name');


// ->buildChartSource()
$t->diag('->buildChartSource()');
$params = array(
    'full_history' => false,
);
$g = newChart($params);
$t->cmp_ok($g->buildChartSource(), '===', true, '->buildChartSource returns "true" if a ChartSource object can be built');

$g = newChart(array('user_id' => $ut->getUserId('user_gb_noCars'),'full_history' => false));
$t->cmp_ok($g->buildChartSource(), '===', false, '->buildChartSource returns "false" if a ChartSource object cannot be built');


// ->getParameter()
$t->diag('->getParameter()');

$g = newChart(array('sdfgsdfgsgd' => 123));
$t->cmp_ok($g->getParameter('sdfgsdfgsgd','aaa'), '===', 123,'->getParameter() returns the requested parameter value if the parameter exists');
$t->cmp_ok($g->getParameter('drasdf'), '===', null,'->getParameter() returns NULL if the parameter is not found and no default value is specified');
$t->cmp_ok($g->getParameter('drasdf','aaa'), '===', 'aaa','->getParameter() returns the specified default value if the parameter is not found');


// ->getChartSourceData()
$t->diag('->getChartSourceData()');
$params = array(
    'categories_list' => array($ut->getIdForCategory('Tax'), $ut->getIdForCategory('Fuel')),
    'vehicles_list' => array($ut->getVehicleId('car-gb-1'),$ut->getVehicleId('car-gb-2'),$ut->getVehicleId('car-gb-3')),
    'full_history' => false,
);
$g = newChart($params);
$data = $g->getChartSourceData('stacked', 'stacked');
$t->cmp_ok(count($data), '<=', 1,'->getChartSourceData() returns a single data serie when both displays are stacked');

$data = $g->getChartSourceData('stacked', 'single');
$t->cmp_ok(count($data), '<=', count($params['categories_list']),'->getChartSourceData() returns at most a number of data series corresponding to the number of categories');


$data = $g->getChartSourceData('single', 'stacked');
$t->cmp_ok(count($data), '<=', count($params['vehicles_list']),'->getChartSourceData() returns at most a number of data series corresponding to the number of vehicles');


$data = $g->getChartSourceData('single', 'single');
$t->cmp_ok(count($data), '<=', count($params['categories_list'])*count($params['vehicles_list']),'->getChartSourceData() returns at most a number of data series corresponding to the number of vehicles multiplied by the number of categories');
$t->isa_ok($data[0], 'ChartDataSerie', '->getChartSourceData() returns one or more series of ChartDataSerie');

$t->diag('->getChartSourceData() when vehicles_list and categories_list are empty');
$params = array(
    'categories_list' => array(),
    'vehicles_list' => array(),
    'full_history' => false,
);
$g = newChart($params);

$v = Doctrine_Core::getTable('Vehicle')->getVehiclesByUserIdQuery($ut->getUserId('user_gb'))->execute();
$c = Doctrine_Core::getTable('Category')->findAll();

$data = $g->getChartSourceData('stacked', 'stacked');
$t->cmp_ok(count(array_keys($data)), '<=', 1,'->getChartSourceData() returns a single data serie when both displays are stacked');

$data = $g->getChartSourceData('stacked', 'single');
$t->cmp_ok(count(array_keys($data)), '<=', count($c),'->getChartSourceData() returns a number of data series corresponding to the number of categories');


$data = $g->getChartSourceData('single', 'stacked');
$t->cmp_ok(count(array_keys($data)), '<=', count($v),'->getChartSourceData() returns a number of data series corresponding to the number of vehicles');


$data = $g->getChartSourceData('single', 'single');
$t->cmp_ok(count(array_keys($data)), '<=', count($c)*count($v),'->getChartSourceData() returns a number of data series corresponding to the number of vehicles multiplied by the number of categories');


$t->diag('->getChartSourceData(), "full_history" parameter');
$params = array(
    'user_id'           => array($ut->getUserId('user_gs')),
    'category_display'  => 'stacked',
    'vehicle_display'   => 'stacked',
    'full_history'      => false,
    'kilometers_from'   => 300,
    'kilometers_to'     => 500,
    'full_history'      => false,
);
$gb = newChart($params);
$data = $gb->getChartSourceData('stacked','stacked');
$rawData = $data[0]->getRawData();
$t->cmp_ok($rawData->count(), '==', 4, 'getChartsQueryResults() When "full_history" is set to "false", the function retrieves only charges in the given range (dates or distances).');

$params = array(
    'user_id'           => array($ut->getUserId('user_gs')),
    'category_display'  => 'stacked',
    'vehicle_display'   => 'stacked',
    'full_history'      => true,
    'kilometers_from'   => 300,
    'kilometers_to'     => 500,
);
$gb = newChart($params);
$data = $gb->getChartSourceData('stacked','stacked');
$rawData = $data[0]->getRawData();
$t->cmp_ok($rawData->count(), '==', 18, 'getChartsQueryResults() When "full_history" is set to "true", the function ignores the starting limit of the range and retrieves the full history of charges.');



// ->checkPath()
$t->diag('checkPath()');
$path = '/test';
sfConfig::set('app_chart_base_path',$path);
sfConfig::set('sf_root_dir',realpath(dirname(__FILE__).'/../..'));
sfConfig::set('sf_web_dir',  sfConfig::get('sf_root_dir').'/web');
$fullpath = sfConfig::get('sf_web_dir').'/images'.$path;
$g  = newChart();
$exist = $g->checkPath($g->getChartBasePath('system'));
$t->ok(file_exists($fullpath), '->checkPath() checks that a path exists. If not, the path is created');

if ($exist) {
$fs = new sfFilesystem();
$fs->remove($fullpath);
}

$g->checkPath($g->getChartBasePath('system'),false);
$t->ok(!file_exists(sfConfig::get('sf_web_dir').$path), '->checkPath() accepts a "create" option. If set to false, the path is not created, if not found.');


try
{
  $g->checkPath($g->getChartBasePath('web'));
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('->checkPath() only accepts system paths');
}

// ->chartSourceIsAvailable()
$t->diag('->chartSourceIsAvailable()');
$g  = newChart();
$t->cmp_ok($g->chartSourceIsAvailable(), '===', false, 'Chart source file is not available for new charts');


$fs = new sfFilesystem();
$fs->touch($g->getChartPath('system'));
$t->cmp_ok($g->chartSourceIsAvailable(), '===', true, 'Chart source file is found, if it exists');
$fs->remove($g->getChartPath('system'));
$fs->remove($g->getChartBasePath('system'));

// ->getAttributes()
$t->diag('->getAttributes()');
$attr = array('test' => 'sdfgdg');
$g  = newChart(array(),array(),$attr);
$t->cmp_ok($g->getAttributes(), '===', $attr, '->getAttributes() returns attributes set via ChartBuilder constructor');

// ->addAttributes()
$t->diag('->addAttributes()');
$attr2 = array('a1234' => '34');
$g->addAttributes($attr2);
$t->cmp_ok($g->getAttributes(), '===', array_merge($attr,$attr2), '->addAttributes() appends new attributes');


// ->setAttributes()
$attr = array('dfsdf' => 'asdfgsdg');
$g->setAttributes($attr);
$t->cmp_ok($g->getAttributes(), '===', $attr, '->setAttributes() resets ChartBuilder attributes and adds new ones');

// ->getParemater()
$t->diag('->getParameter()');
$p = array('test' => 'sdfgdg','asdgdf' => 'asdtert');
$g  = newChart($p);
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


// ->setParameter()
$t->diag('->setParameter()');
$g->setParameters(array('hjkfghkd' => 'arasd'));
$t->cmp_ok($g->getParameter('hjkfghkd'), '===', 'arasd', '->setParameter() creates or modify a parameter');

// ->getLogger()
$t->diag('->getLogger()');
$g = newChart();
$t->cmp_ok($g->getLogger(), '===', sfContext::getInstance()->getLogger(), 'The default logger is set by the application');

// ->setLogger()
$logger = new sfNoLogger(new sfEventDispatcher());
$g->setLogger($logger);
$t->cmp_ok($g->getLogger(), '===', $logger, '->setLogger() allows to define a custom logger');


// ->generate()
$t->diag('->generate()');
$g = newChart(array('user_id' => $ut->getUserId('user_gb_noCars'),'full_history' => false));
$t->cmp_ok($g->generate(), '===', false, 'If the User has no cars, the chart cannot be generated.');

$g = newChart();
$t->ok($g->generate(), '->generate() Returns data required to build the chart.');

// ->display()
$t->diag('->display()');
$g = newChart(array('user_id' => $ut->getUserId('user_gb_noCars')));
$t->like($g->display(), '/Not enough data/', 'If the User has no cars, a message id displayed instead of the chart.');

$g = newChart();
$t->like($g->display(), '/This function cannot/', 'Even if the User has cars, nothing is generated by ChartBuilder. This task is left to childrens.');


// ->doForceGenerate()
$t->diag('->doForceGenerate()');
$g = newChart();
sfConfig::clear('app_charts_force_generate');
$t->cmp_ok($g->doForceGenerate(), '===', false, '->doForceGenerate() returns false by default');

sfConfig::set('app_charts_force_generate', true);
$t->cmp_ok($g->doForceGenerate(), '===', true, '->doForceGenerate() returns the value set in app_charts_force_generate');






function getData($data = array()) {

    $fields = Doctrine_Core::getTable('Chart')->getFieldNames();

    $defaults = array_combine($fields, array_fill(0, count($fields), null));

    unset(
            $defaults['created_at'],
            $defaults['updated_at'],
            $defaults['slug'],
            $defaults['id']
    );

    $foreign = array(
        'vehicles_list' => null,
        'categories_list' => null,
    );

    $defaults = array_merge($defaults, $foreign);

    return array_merge($defaults, $data);
}

function newChart($data = array(),$options = array(),$attributes = array()) {

    $ut = new otokouTestFunctional(new sfBrowser());

    $data = array_merge(
                    array(
                        'user_id'           => $ut->getUserId('user_gb'),
                        'vehicle_display'   => 'single',
                        'category_display'  => 'stacked',
                        'range_type'        => 'distance',
                        'format'            => 'png',
                        'chart_name'        => 'cost_per_year',
                        //'full_history'      => 'false',
                    ),
                    $data);

    return new ChartBuilder(getData($data),$options,$attributes);
}