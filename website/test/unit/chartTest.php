<?php
include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../../lib/test/otokouTestFunctional.class.php';

$ut = new otokouTestFunctional(new sfBrowser());

$t = new lime_test(11, new lime_output_color());




// ->getHash()
$t->diag('->getHash');

$g = new Chart();
$sha = $g->getHash();

$t->isa_ok($sha, 'string','->getHash() generates a sha key even for empty charts');
$t->cmp_ok(strlen($sha), '==', 40,'->getHash() generates a sha key of length 40');


$g = new Chart();
$g->setFormat('png');
$g->setKilometersTo(12423);
$g->setUserId($ut->getUserId('ruf'));
$g->save();
$g->link('Vehicles', array($ut->getVehicleId('vw-touran-1-4-tsi')));
$g->link('Categories',array($ut->getIdForCategory('Tax')));
$t->isa_ok($g->getHash(), 'string','->getHash() generates a sha key when fields are set');
$t->cmp_ok(strlen($g->getHash()), '==', 40,'->getHash() generates a sha key of length 40');

$sha1 = $g->getHash();
$g->setKilometersFrom(12343);
$g->save();
$sha2 = $g->getHash();
$t->isnt($sha1, $sha2, '->getHash() creates a sha key in function of Chart content');


// ->save()
$t->diag('->save()');

$g = new Chart();
$g->setFormat('png');
$g->setKilometersTo(12423);
$g->setUserId($ut->getUserId('ruf'));
$g->link('Vehicles', array($ut->getVehicleId('vw-touran-1-4-tsi')));
$g->link('Categories',array($ut->getIdForCategory('Tax')));
$g->save();
$sha1 = $g->getHash();

$t->isa_ok($sha1, 'string','->save() creates a sha for each chart');

$g->setKilometersFrom(235);
$g->save();
$sha2 = $g->getHash();

$t->cmp_ok($sha1, '!=', $sha2,'->save() The hash field is updated each time the object is changed');


$g = new Chart();
$g->setFormat('png');
$g->setKilometersTo(12423);
$g->setUserId($ut->getUserId('ruf'));
$g->link('Vehicles', array($ut->getVehicleId('vw-touran-1-4-tsi')));
$g->link('Categories',array($ut->getIdForCategory('Fuel')));
$sha = $g->getHash();

$g2 = new Chart();
$g2->setUserId($ut->getUserId('ruf'));
$g->save();
$finalsha = $g->getHash();
$t->isnt($sha, $finalsha, 'When saving the object, ->save() checks that a unique sha is set. If not, a new one is generated.');


// ->delete()
$t->diag('->delete()');
$g = new Chart();
$g->setUserId($ut->getUserId('ruf'));
$g->save();
$id = $g->getId();
$path = $g->getChartPath('system');
$fs = new sfFilesystem(new sfEventDispatcher());
$fs->touch($path);

$t->cmp_ok(file_exists($path), '===', true, 'A Chart may have an associated figure file.');

$g->delete();
$g2 = Doctrine_Core::getTable('Chart')->findOneById($id);

$t->cmp_ok($g2, '===', false, '->delete() deletes the chart from the DB');
$t->cmp_ok(file_exists($path), '===', false, '->delete() also deletes the image associated to the Chart');
