<?php

include dirname(__FILE__) . '/../bootstrap/unit.php';

//new sfDatabaseManager($configuration);
//Doctrine_Core::loadData(sfConfig::get('sf_test_dir') . '/fixtures');
//
//$ut = new otokouTestFunctional(new sfBrowser());


$t = new lime_test(2, new lime_output_color());

// $gs->getSeriesCount()
$t->diag('$gs->getSeriesCount()');
$gs = new GraphSource();
$t->cmp_ok($gs->getSeriesCount(), '===', null, '>getSeriesCount() returns a value only if raw_data parameter is set');

$data = array(
    0 => array(1,2,3),
    1 => array(3,4,5),
);
$gs->setParam('raw_data', $data);
$t->cmp_ok($gs->getSeriesCount(), '===', 2, '>getSeriesCount() counts the number of series in the raw_data array');
