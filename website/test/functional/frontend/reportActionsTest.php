<?php

include(dirname(__FILE__) . '/../../bootstrap/functional.php');

$browser = new otokouTestFunctional(new sfBrowser());
$browser->loadData();
$browser->setTester('doctrine', 'sfTesterDoctrine');

$browser->
info('1 - Security')->
        get('/ruf/reports')->
            with('request')->begin()->
                isParameter('module', 'report')->
                isParameter('action', 'index')->
            end()->
            with('response')->begin()->
                isStatusCode(401)->
            end()->
        login('user_gs', 'user')->
        get('/reports')->
            with('response')->begin()->
                isStatusCode(404)->
            end()->
        get('/ruf/reports')->
            with('response')->begin()->
                isStatusCode(403)->
            end()->
        get('/user_gs/reports')->
            with('request')->begin()->
                isParameter('module', 'report')->
                isParameter('action', 'index')->
            end()
        ->logout()


->info('2 - Index')
    ->login('ruf', 'admin@1')
    ->get('/ruf/reports')
        ->with('response')
        ->begin()
            ->isStatusCode(200)
            ->checkElement('h2:contains("Vehicles")',true)
            ->checkElement('ul.vehicles_menu li.vehicle_archived',0)
            ->checkElement('ul.vehicles_menu li.vehicle_active',1)
            ->checkElement('body:contains("No new reports available")',true)
        ->end()
    ->logout()
    ->login('user_gs', 'user')
    ->get('/user_gs/reports')
        ->with('response')
        ->begin()
            ->isStatusCode(200)
            ->checkElement('h2:contains("Vehicles")',true)
            ->checkElement('ul.vehicles_menu li.vehicle_archived',1)
            ->checkElement('ul.vehicles_menu li.vehicle_active',1)
            ->checkElement('body:contains("No new reports available")',false)
            ->checkElement('ul.reports_list',true)
            ->checkElement('ul.reports_list li',1)
        ->end()

        ;
