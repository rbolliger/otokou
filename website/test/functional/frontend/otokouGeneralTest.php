<?php

include(dirname(__FILE__) . '/../../bootstrap/functional.php');

$browser = new otokouTestFunctional(new sfBrowser());
$browser->loadData()->login();



$browser->
        info('0 - The homepage is redirected to charges/new')->
        get('/')->
        with('request')->
        begin()->
        isParameter('module', 'charges')->
        isParameter('action', 'new')->
        end();
