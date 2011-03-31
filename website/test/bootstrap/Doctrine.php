<?php

include(dirname(__FILE__).'/unit.php');
$configuration = ProjectConfiguration::getApplicationConfiguration( 'frontend', 'test', true);
new sfDatabaseManager($configuration);

//Doctrine_Manager::getInstance()->createDatabases('doctrine');

Doctrine::createTablesFromModels(dirname(__FILE__).'/../../lib/model');
Doctrine::loadData(sfConfig::get('sf_test_dir').'/fixtures');