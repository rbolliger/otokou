<?php

include dirname(__FILE__) . '/../bootstrap/unit.php';

//new sfDatabaseManager($configuration);
//Doctrine_Core::loadData(sfConfig::get('sf_test_dir') . '/fixtures');
//
//$ut = new otokouTestFunctional(new sfBrowser());


$t = new lime_test(4, new lime_output_color());


// ->get*
$t->diag('get*');

$serie = array(
    'id' => 2,
    'raw_data' => array(1,2,3,4,5),
    'label' => 'adfgdfghdf',
);
$gs = new GraphDataSerie($serie);
$t->cmp_ok($gs->getId(), '===', $serie['id'], '->getId() returns the value set in the constructor');
$t->cmp_ok($gs->getRawData(), '===', $serie['raw_data'], '->getRawData() returns the value set in the constructor');
$t->cmp_ok($gs->getLabel(), '===', $serie['label'], '->getLabel() returns the value set in the constructor');


// __constructor
$t->diag('::__construct()');
$serie = array(
    'idsg' => 2,
    'ase' => array(1,2,3,4,5),
    'ws' => 'adfgdfghdf',
);
try
{
  $gs = new GraphDataSerie($serie);
  $t->fail('no code should be executed after throwing an exception');
}
catch (Exception $e)
{
  $t->pass('::__construct warns if the user tries to pass invalid parameters');
}

