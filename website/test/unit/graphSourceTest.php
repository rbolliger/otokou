<?php

include dirname(__FILE__) . '/../bootstrap/unit.php';

new sfDatabaseManager($configuration);
Doctrine_Core::loadData(sfConfig::get('sf_test_dir') . '/fixtures');
//
//$ut = new otokouTestFunctional(new sfBrowser());


$t = new lime_test(26, new lime_output_color());


// ->setParam()
$t->diag('->setParam()');
$g = new GraphSource();
$g->setParam('dfsdf','asdfgsdg');
$t->cmp_ok($g->getParam('dfsdf'), '===', 'asdfgsdg', '->setParam() sets the defined parameter');

// ->addParams()
$t->diag('->addParams()');
$p = array('a1234' => '34', 'wrter' => 'wetwet', 'ysrw34' => 'asgsdf');
$g->addParams($p);
$t->cmp_ok($g->getParam('a1234'), '===', '34', '->addParams() allows to set multiple parameters via an array');


// ->getSeries()
$t->diag('->getSeries()');
$gs = new GraphSource();
try
{
  $gs->getSeries();
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('->getSeries() require series to be set.');
}

// ->setSeries()
$t->diag('->setSeries()');
$series = array();
$series[] = new GraphDataSerie(array('raw_data' => array(1,2,3)));
$series[] = new GraphDataSerie(array('raw_data' => array(3,4,5)));

$gs->setSeries($series);
$t->cmp_ok($gs->getSeries(), '===', $series,'->getSeries() returns series set by setSeries()');


// ->getSeriesCount()
$t->diag('->getSeriesCount()');
$gs = new GraphSource();
$t->cmp_ok($gs->getSeriesCount(), '===', null, '->getSeriesCount() returns a value only if raw_data parameter is set');

$series = array();
$series[] = new GraphDataSerie(array('raw_data' => array(1,2,3)));
$series[] = new GraphDataSerie(array('raw_data' => array(3,4,5)));
$gs->setSeries($series);
$t->cmp_ok($gs->getSeriesCount(), '===', 2, '->getSeriesCount() counts the number of series in the raw_data array');


// ->getSeriesDataByColumn()
$t->diag('->getSeriesDataByColumn()');
$data = Doctrine_Core::getTable('Charge')->findAll();
$gs = new GraphSource();

try
{
  $gs->getSeriesDataByColumn('kilometers');
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('->getSeriesDataByColumn() Raw data must be set before retrieving series');
}

$series = array(new GraphDataSerie(array('raw_data' => $data)));
$gs->setSeries($series);
try
{
  $gs->getSeriesDataByColumn('quantity');
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('->getSeriesDataByColumn() All rows must be defined in charges table to build sets for a given column');
}


$series = $gs->getSeriesDataByColumn('kilometers');
$t->cmp_ok(count($series), '===', 1,'->getSeriesDataByColumn() returns an array containing the requested data for each data serie');
$t->cmp_ok(count($series[0]), '===', count($data), '->getSeriesDataByColumn() returns a value for each element in raw_data');



$dates = array();
$dates_ts = array();
foreach ($data as $charge) {
    $dates[] = $charge->getDate();
    $dates_ts[] = strtotime($charge->getDate());
}

$dt = $gs->getSeriesDataByColumn('date');
$t->cmp_ok($dt[0], '===', $dates,'By default, ->getSeriesDataByColumn() returns unmodified data');

$dt = $gs->getSeriesDataByColumn('date','datetime');
$t->cmp_ok($dt[0], '===', $dates_ts,'By setting "datetime" option, ->getSeriesDataByColumn() returns data formatted as timestamps');



$series = array(new GraphDataSerie(array('raw_data' => $data)),new GraphDataSerie(array('raw_data' => $data)),new GraphDataSerie(array('raw_data' => $data)));
$gs->setSeries($series);
$series = $gs->getSeriesDataByColumn('kilometers');
$t->cmp_ok(count(array_keys($series)), '===', 3, '->getSeriesDataByColumn() returns a data serie for each raw data serie stored.');
$t->cmp_ok(array(count($series[0]),count($series[1]),count($series[2])), '===', array(count($data),count($data),count($data)), '->getSeriesDataByColumn() returns a value for each element in raw_data');

// ::filterValuesLargerThan()
$t->diag('::filterValuesLargerThan()');
$data = array(1,2,3,4,5,6,7,8);

$keys = GraphSource::filterValuesLargerThan($data, 5);
$t->cmp_ok($keys, '===', array(1,2,3,4,5),'::filterValuesLargerThan() returns the elements of the input array whose value is lower than the given bound');


// ::filterValuesDifferentThan()
$t->diag('::filterValuesDifferentThan()');
$data = array(1,1.1,0.9,2,3,1,1);

$keys = GraphSource::filterValuesDifferentThan($data, 1);
$t->cmp_ok($keys, '===', array(0 => 1,5 => 1,6 => 1),'::filterValuesDifferentThan() returns the elements of the input array whose value is equal than the given bound');

// ::filterValuesOutsideRange
$t->diag('::filterValuesOutsideRange()');
$data = array(1,2,3,4,5,6,7,1,3,4);

$keys = GraphSource::filterValuesOutsideRange($data, 1, 4);
$t->cmp_ok($keys, '===', array(0 => 1,1 => 2,2 => 3,7 => 1,8 => 3),'::filterValuesOutsideRange() returns the elements of the input array whose value is equal or larger than the lower bound and lower of the upper bound');



// ->buildXAxisData()
$t->diag('->buildXAxisData()');

$q = Doctrine_Core::getTable('Charge')->createQuery('c')
        ->select('c.date')
        ->leftJoin('Category ct')
        ->where('ct.Name = ?');

$e1 = $q->execute(array('Tax'));
$e2 = $q->execute(array('Fuel'));

$s1 = new GraphDataSerie(array('raw_data' => $e1));
$s2 = new GraphDataSerie(array('raw_data' => $e2));

$c1 = $q->execute(array('Tax'),Doctrine_Core::HYDRATE_ARRAY);

$d1 = array();
foreach ($c1 as $key => $value) {
    $d1[] = strtotime($value['date']);
}

$c2 = $q->execute(array('Fuel'),Doctrine_Core::HYDRATE_ARRAY);

$d2 = array();
foreach ($c2 as $key => $value) {
    $d2[] = strtotime($value['date']);
}

$dates = array_unique(array_merge($d1,$d2));
sort($dates);

$g = new GraphSource();
$g->setSeries(array($s1,$s2));
$x_series = $g->getSeriesDataByColumn('date','datetime');
$x = $g->buildXAxisData($x_series);
$t->cmp_ok($x, '===', $dates, '->buildXAxisData() returns an array containing the unique values of all series data for the requestes column');


// buildXAxisDataByRangeTypeAndCalculationBase()
$t->diag('::buildXAxisDataByRangeTypeAndCalculationBase');

$rt = 'anything';
$bt = 'date';
$options = array();
try
{
  $x = $g->buildXAxisDataByRangeTypeAndCalculationBase($rt, $bt, $options);
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('->buildXAxisDataByRangeTypeAndCalculationBase() anly accepts range types defined by GraphTable::getRangeTypes()');
}

$rt = 'date';
$bt = 'sdgsd';
$options = array();
try
{
  $x = $g->buildXAxisDataByRangeTypeAndCalculationBase($rt, $bt, $options);
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('->buildXAxisDataByRangeTypeAndCalculationBase() anly accepts base types defined by GraphTable::getRangeTypes()');
}


$rt = 'date';
$bt = 'date';
$options = array();
$x = $g->buildXAxisDataByRangeTypeAndCalculationBase($rt, $bt, $options);

$t->isa_ok($x, 'array', '->buildXAxisDataByRangeTypeAndCalculationBase() returns an array');

$t->cmp_ok($x['value'], '===', $x['base'], '->buildXAxisDataByRangeTypeAndCalculationBase() returns the same value in "base" and "value", if range and base types are the same.');


$rt = 'distance';
$bt = 'distance';
$options = array();
$x = $g->buildXAxisDataByRangeTypeAndCalculationBase($rt, $bt, $options);
$t->cmp_ok($x['value'], '===', $x['base'], '->buildXAxisDataByRangeTypeAndCalculationBase() returns the same value in "base" and "value", if range and base types are the same.');

$rt = 'date';
$bt = 'distance';
$options = array();
$x = $g->buildXAxisDataByRangeTypeAndCalculationBase($rt, $bt, $options);
$t->cmp_ok(count($x['value']), '===', count($x['base']), '->buildXAxisDataByRangeTypeAndCalculationBase() "value" and "base" fields have the same size.');

$rt = 'distance';
$bt = 'distance';
$options = array(
    'check_zeroes' => false,
);
$x = $g->buildXAxisDataByRangeTypeAndCalculationBase($rt, $bt, $options);
$t->cmp_ok(count(array_keys($x['base'],0)), '===', 1, '->buildXAxisDataByRangeTypeAndCalculationBase() When "check_zeroes" is set to false, zeroes may appear in "base" field.');


$options = array(
    'check_zeroes' => true,
    'zero_approx'  => -1234,
);
$x = $g->buildXAxisDataByRangeTypeAndCalculationBase($rt, $bt, $options);
$t->cmp_ok(count(array_keys($x['base'],-1234)), '===', 1, '->buildXAxisDataByRangeTypeAndCalculationBase() When "check_zeroes" is set to true, zeroes are repalced with the value set in "zero_approx".');
