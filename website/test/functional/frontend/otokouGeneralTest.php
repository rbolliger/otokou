<?php

include(dirname(__FILE__) . '/../../bootstrap/functional.php');
include dirname(__FILE__) . '/../../../lib/test/otokouTestFunctional.class.php';

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
        end()->
        
        
        
        
        info('2 - Top Menu')->
        
        info('  2.1 - A login link is displayed when the user is not authentified')->
        logout()->
        get('/')->
        with('response')->
        begin()->
        checkElement('a #login','/Login/')->
        end()->
        
        info('  2.2 - A logout link is NOT displayed when the user is not authentified')->
        get('/')->
        with('response')->
        begin()->
        checkElement('a #logout',false)->
        end()->
        
        
        info('  2.3 - A logout link is displayed when the user is authentified')->
        login()->
        get('/')->
        with('response')->
        begin()->
        checkElement('a #logout','/Logout/')->
        end()->
        
        info('  2.4 - A login link is NOT displayed when the user is not authentified')->
        get('/')->
        with('response')->
        begin()->
        checkElement('a #login',false)->
        end()
        
        
        
        
        ;
        
        


