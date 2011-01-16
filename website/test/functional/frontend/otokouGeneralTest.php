<?php

include(dirname(__FILE__) . '/../../bootstrap/functional.php');

$browser = new otokouTestFunctional(new sfBrowser());
$browser->loadData();



$browser->
        info('1 - Homepage')->
        
        info('  1.1 - Authenthicated User: the homepage is charges/new')->
        login()->
        get('/')->
        with('response')->
        begin()->
        checkElement('h1','Add a New Charge')->
        end()->
        
     
        info('  1.2 - Anonymous User: the homepage is homepage/index')->
        logout()->
        get('/')->
        with('request')->
        begin()->
        isParameter('module', 'homepage')->
        isParameter('action', 'index')->
        end()->
        with('response')->
        begin()->
        checkElement('h1','/Hello/')->
        end();
        
        


