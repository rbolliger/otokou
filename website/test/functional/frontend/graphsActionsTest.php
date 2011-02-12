<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new otokouTestFunctional(new sfBrowser());
$browser->loadData();
$browser->setTester('doctrine', 'sfTesterDoctrine');

$browser->
        
        info('1 - Index')->
        get('/ruf/graphs')->
          with('request')->begin()->
            isParameter('module', 'graphs')->
            isParameter('action', 'index')->
          end()->
          with('response')->begin()->
            isStatusCode(401)->
          end()->

        login('user_graphs','user')->
        get('/graphs')->
          with('response')->begin()->
            isStatusCode(404)->
          end()->
        
        get('/ruf/graphs')->
          with('response')->begin()->
            isStatusCode(403)->
          end()->
        
        get('/user_graphs/graphs')->
          with('request')->begin()->
            isParameter('module', 'graphs')->
            isParameter('action', 'index')->
          end()->
          with('response')->begin()->
            isStatusCode(200)->
          end()->
        
        info('2 - Filters')->
        
        info('  2.1 - Blank filtering')->
        Click('Filter')->
        with('request')->
            begin()->
                isParameter('module','graphs')->
                isParameter('action','filter')->
            end()->
        with('form')->
            begin()->
                hasErrors(false)->
            end()->
        with('response')->
            begin()->
                isRedirected()->
                followRedirect()->
            end()->
        with('response')->
            begin()->
            // two vehicles listed, including archived one
                checkElement('div.graphs_filters tr input[name="charge_filters[vehicle_id][]"]',2)->
            end()->
        
        info('  2.2 - Filter action')->
        Click('Filter',array(
            'charge_filters' => array(
                'vehicle_id' => array($browser->getVehicleId('car-graphs-1')),
                'vehicle_display' => 'stacked',
                'category_id' => array($browser->getIdForCategory('Tax'),$browser->getIdForCategory('Fuel')),
                'category_display' => 'single',
            )
        ))->
            with('form')->
            begin()->
                hasErrors(false)->
            end()->
        with('response')->
            begin()->
                isRedirected()->
                followRedirect()->
            end()->
        with('response')->
            begin()->
            // two vehicles listed, including archived one
                checkElement('div.graphs_filters tr input[name="charge_filters[vehicle_id][]"][checked="checked"]',1)->
                checkElement('div.graphs_filters tr input[name="charge_filters[vehicle_display]"][checked="checked"]',1)->
                checkElement('div.graphs_filters tr input[name="charge_filters[category_id][]"][checked="checked"]',2)->
                checkElement('div.graphs_filters tr input[name="charge_filters[category_display]"][checked="checked"]',1)->
            end()
;
