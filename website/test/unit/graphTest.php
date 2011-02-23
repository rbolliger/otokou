<?php
include dirname(__FILE__) . '/../bootstrap/unit.php';

new sfDatabaseManager($configuration);

$ut = new otokouTestFunctional(new sfBrowser());

$t = new lime_test(8, new lime_output_color());




// ->generateSha()
$t->diag('->generateSha');

$g = new Graph();
$sha = $g->generateSha();

$t->isa_ok($sha, 'string','->generateSha() generates a sha key even for empty graphs');
$t->cmp_ok(strlen($sha), '==', 40,'->generateSha() generates a sha key of length 40');


$g = new Graph();
$g->setFormat('png');
$g->setKilometersTo(12423);
$g->setUserId($ut->getUserId('ruf'));
$g->link('Vehicles', array($ut->getVehicleId('vw-touran-1-4-tsi')));
$g->link('Categories',array($ut->getIdForCategory('tax')));
$t->isa_ok($g->generateSha(), 'string','->generateSha() generates a sha key when fields are set');
$t->cmp_ok(strlen($g->generateSha()), '==', 40,'->generateSha() generates a sha key of length 40');

$sha1 = $g->generateSha();
$g->setKilometersFrom(12343);
$sha2 = $g->generateSha();
$t->isnt($sha1, $sha2, '->generateSha() creates a sha key in function of Graph content');


// ->save()
$t->diag('->save()');

$g = new Graph();
$g->setFormat('png');
$g->setKilometersTo(12423);
$g->setUserId($ut->getUserId('ruf'));
$g->link('Vehicles', array($ut->getVehicleId('vw-touran-1-4-tsi')));
$g->link('Categories',array($ut->getIdForCategory('tax')));
$g->save();
$sha1 = $g->getSha();

$t->isa_ok($sha1, 'string','->save() creates a sha for each graph');

$g->setKilometersFrom(235);
$g->save();
$sha2 = $g->getSha();

$t->cmp_ok($sha1, '===', $sha2,'->save() Once set, the sha field remains the same, even if the object is changed');


$g = new Graph();
$g->setFormat('png');
$g->setKilometersTo(12423);
$g->setUserId($ut->getUserId('ruf'));
$g->link('Vehicles', array($ut->getVehicleId('vw-touran-1-4-tsi')));
$g->link('Categories',array($ut->getIdForCategory('fuel')));
$sha = $g->generateSha();

$g2 = new Graph();
$g2->setUserId($ut->getUserId('ruf'));
$g2->setSha($sha);
$g2->save();

$g->save();
$finalsha = $g->getSha();
$t->isnt($sha, $finalsha, 'When saving the object, ->save() checks that a unique sha is set. If not, a new one is generated.');