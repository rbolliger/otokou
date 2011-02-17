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
                checkElement('div.graphs_filters tr input[name="graph_filters[vehicle_id][]"]',2)->
                checkElement('#filter_values_vehicle_id:contains("nothing")]',true)->
                checkElement('#filter_values_vehicle_display:contains("nothing")]',true)->
                checkElement('#filter_values_category_id:contains("nothing")]',true)->
                checkElement('#filter_values_category_display:contains("nothing")]',true)->
                checkElement('#filter_values_range_type:contains("nothing")]',true)->
                checkElement('#filter_values_date_range:contains("nothing")]',true)->
                checkElement('#filter_values_kilometers_range:contains("nothing")]',true)->
                checkElement('table#query_results:contains("No elements found")]',true)->
            end()->
        
        info('  2.2 - Filter action')->
        Click('Filter',array(
            'graph_filters' => array(
                'vehicle_id' => array($browser->getVehicleId('car-graphs-1df',false)),
                'vehicle_display' => 'anything',
                'category_id' => array($browser->getIdForCategory('Taxfjh',false),$browser->getIdForCategory('Fuel',false)),
                'category_display' => 'ydfghdfgzh',
                'range_type' => 'gdfg',
                'date_range' => array('from' => 'asdraxd', 'to' => '235434'),
                'kilometers_range' => array('from' => 'asdraxd', 'to' => '235434'),
            )
        ))->
            with('form')->
            begin()->
                hasErrors(7)->
                isError('vehicle_id','/invalid/')->
                isError('vehicle_display','/invalid/')->
                isError('category_id','/invalid/')->
                isError('category_display','/invalid/')->
                isError('range_type','/invalid/')->
                isError('date_range','/invalid/')->
                isError('kilometers_range','/invalid/')->
            end()->
        Click('Filter',array(
            'graph_filters' => array(
                'vehicle_id' => array($browser->getVehicleId('car-graphs-1')),
                'vehicle_display' => 'stacked',
                'category_id' => array($browser->getIdForCategory('Tax'),$browser->getIdForCategory('Fuel')),
                'category_display' => 'single',
                'range_type' => 'date',
                'date_range' => array('from' => '', 'to' => ''),
                'kilometers_range' => array('from' => '', 'to' => ''),
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
        with('request')->
            begin()->
                isParameter('module','graphs')->
                isParameter('action','index')->
            end()->
        with('response')->
            begin()->
            // two vehicles listed, including archived one
                checkElement('div.graphs_filters tr input[name="graph_filters[vehicle_id][]"][checked="checked"]',1)->
                checkElement('div.graphs_filters tr input[name="graph_filters[vehicle_display]"][checked="checked"]',1)->
                checkElement('div.graphs_filters tr input[name="graph_filters[category_id][]"][checked="checked"]',2)->
                checkElement('div.graphs_filters tr input[name="graph_filters[category_display]"][checked="checked"]',1)->
                checkElement('#filter_values_vehicle_id:contains("'.$browser->getVehicleId('car-graphs-1').'")]',true)->
                checkElement('#filter_values_vehicle_display:contains("stacked")]',true)->
                checkElement('#filter_values_category_id:contains("'.$browser->getIdForCategory('Tax').', '.$browser->getIdForCategory('Fuel').'")]',true)->
                checkElement('#filter_values_category_display:contains("single")]',true)->
                checkElement('#filter_values_range_type:contains("date")]',true)->
                checkElement('#filter_values_date_range:contains("nothing")]',true)->
                checkElement('#filter_values_kilometers_range:contains("nothing")]',true)->
                checkElement('table#query_results:contains("No elements found")]',true)->
            end()
;
