<?php

include dirname(__FILE__) . '/../bootstrap/unit.php';


$t = new lime_test(6, new lime_output_color());


// ->get*
$t->diag('get*');

$serie = array(
    'id' => 2,
    'raw_data' => array(1,2,3,4,5),
    'label' => 'adfgdfghdf',
    'vehicle_id' => array(1,3),
    'category_id' => array(2,4,6),
);
$gs = new GraphDataSerie($serie);
$t->cmp_ok($gs->getId(), '===', $serie['id'], '->getId() returns the value set in the constructor');
$t->cmp_ok($gs->getRawData(), '===', $serie['raw_data'], '->getRawData() returns the value set in the constructor');
$t->cmp_ok($gs->getLabel(), '===', $serie['label'], '->getLabel() returns the value set in the constructor');
$t->cmp_ok($gs->getVehicleId(), '===', $serie['vehicle_id'], '->getVehicleId() returns the value set in the constructor');
$t->cmp_ok($gs->getCategoryId(), '===', $serie['category_id'], '->getCategoryId() returns the value set in the constructor');


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

