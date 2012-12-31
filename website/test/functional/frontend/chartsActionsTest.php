<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
include dirname(__FILE__) . '/../../../lib/test/otokouTestFunctional.class.php';

$browser = new otokouTestFunctional(new sfBrowser());
$browser->loadData();
$browser->setTester('doctrine', 'sfTesterDoctrine');

$browser->
        
        info('1 - Index')->
        get('/ruf/charts')->
          with('request')->begin()->
            isParameter('module', 'charts')->
            isParameter('action', 'index')->
          end()->
          with('response')->begin()->
            isStatusCode(401)->
          end()->

        login('user_charts','user')->
        get('/charts')->
          with('response')->begin()->
            isStatusCode(404)->
          end()->
        
        get('/ruf/charts')->
          with('response')->begin()->
            isStatusCode(403)->
          end()->
        
        get('/user_charts/charts')->
          with('request')->begin()->
            isParameter('module', 'charts')->
            isParameter('action', 'index')->
          end()->
          with('response')->begin()->
            isStatusCode(200)->
            checkElement('div#main img',false)->
            checkElement('body:contains("Warning")',false)->
            checkElement('body:contains("Notice")',false)->
            checkElement('table#filter_values:contains("No elements found")',true)->
            checkElement('table#query_results:contains("No elements found")',true)->
            checkElement('div.vehicle_statistics',2)->
            checkElement('div.vehicle_statistics div.overall_cost',2)->
            checkElement('div.vehicle_statistics div.overall_cost div.value','/\d+\.\d\d/',array('position' => 1))->
            checkElement('div.vehicle_statistics div.traveled_distance',2)->
            checkElement('div.vehicle_statistics div.traveled_distance div.value','/\d+\.\d\d/',array('position' => 1))->
            checkElement('div.vehicle_statistics div.cost_per_km',2)->
            checkElement('div.vehicle_statistics div.cost_per_km div.value','/\d+\.\d\d/',array('position' => 1))->
            checkElement('div.vehicle_statistics div.fuel_consumption',2)->
            checkElement('div.vehicle_statistics div.fuel_consumption div.value','/\d+\.\d\d/',array('position' => 1))->
          end()->
        
        info('2 - Filters')->
        
        info('  2.1 - Blank filtering')->
        Click('Filter')->
        with('request')->
            begin()->
                isParameter('module','charts')->
                isParameter('action','filter')->
            end()->
        with('form')->
            begin()->
                hasErrors(false)->
            end()->
        with('doctrine')->
            begin()->
                check('Chart',array('user_id' => $browser->getUserId('user_charts')),0)->
            end()->
        with('response')->
            begin()->
                isRedirected()->
                followRedirect()->
            end()->
        with('request')->
            begin()->
                isParameter('module','charts')->
                isParameter('action','index')->
            end()->
        with('doctrine')->
            begin()->
                check('Chart',array('user_id' => $browser->getUserId('user_charts')),0)->
            end()->
        with('response')->
            begin()->
                checkElement('body:contains("Warning")]',false)->
                checkElement('body:contains("Notice")]',false)->
            // two vehicles listed, including archived one
                checkElement('div#charts_filters tr input[name="chart_filters[vehicles_list][]"]',2)->
                checkElement('#filter_values_vehicles_list:contains("nothing")',true)->
                checkElement('#filter_values_vehicle_display:contains("nothing")',true)->
                checkElement('#filter_values_categories_list:contains("nothing")',true)->
                checkElement('#filter_values_category_display:contains("nothing")',true)->
                checkElement('#filter_values_range_type:contains("nothing")',true)->
                checkElement('#filter_values_date_range:contains("nothing")',true)->
                checkElement('#filter_values_kilometers_range:contains("nothing")',true)->
                checkElement('#filter_values_chart_name',false)->
                checkElement('table#query_results tbody tr',1)->
                checkElement('table#filter_values tbody tr',7)->
                checkElement('#charts_filters table tbody tr',7)->
                checkElement('div.vehicle_statistics',2)->
                checkElement('div.vehicle_statistics div.overall_cost',2)->
                checkElement('div.vehicle_statistics div.traveled_distance',2)->
                checkElement('div.vehicle_statistics div.cost_per_km',2)->
                checkElement('div.vehicle_statistics div.fuel_consumption',2)->
            end()->
        
        
        info('  2.2 - Filter action')->
        Click('Filter',array(
            'chart_filters' => array(
                'vehicles_list' => array($browser->getVehicleId('car-charts-1df',false)),
                'vehicle_display' => 'anything',
                'categories_list' => array($browser->getIdForCategory('Taxfjh',false),$browser->getIdForCategory('Fuel',false)),
                'category_display' => 'ydfghdfgzh',
                'range_type' => 'gdfg',
                'date_range' => array('from' => 'asdraxd', 'to' => '235434'),
                'kilometers_range' => array('from' => 'asdraxd', 'to' => '235434'),
            )
        ))->
            with('form')->
            begin()->
                hasErrors(7)->
                isError('vehicles_list','/invalid/')->
                isError('vehicle_display','/invalid/')->
                isError('categories_list','/invalid/')->
                isError('category_display','/invalid/')->
                isError('range_type','/invalid/')->
                isError('date_range','/invalid/')->
                isError('kilometers_range','/invalid/')->
            end()->
            with('doctrine')->
            begin()->
                check('Chart',array('user_id' => $browser->getUserId('user_charts')),0)->
            end()->
            with('response')->
            begin()->
                checkElement('table#query_results:contains("No elements found")]',true)->
            end()->
        Click('Filter',array(
            'chart_filters' => array(
                'vehicles_list' => array($browser->getVehicleId('car-charts-1')),
                'vehicle_display' => 'stacked',
                'categories_list' => array($browser->getIdForCategory('Tax'),$browser->getIdForCategory('Fuel')),
                'category_display' => 'single',
                'range_type' => 'date',
                'date_range' => array('from' => '1234', 'to' => ''),
                'kilometers_range' => array('from' => '1234', 'to' => ''),
            )
        ))->
            with('form')->
            begin()->
                hasErrors(2)->
                isError('date_range','/Only one/')->
                isError('kilometers_range','/Only one/')->
            end()->
        Click('Filter',array(
            'chart_filters' => array(
                'vehicles_list' => array($browser->getVehicleId('car-charts-1')),
                'vehicle_display' => 'stacked',
                'categories_list' => array($browser->getIdForCategory('Tax'),$browser->getIdForCategory('Fuel')),
                'category_display' => 'single',
                'range_type' => 'date',
                'date_range' => array('from' => '', 'to' => '56'),
                'kilometers_range' => array('from' => '0', 'to' => '34535'),
            )
        ))->
            with('form')->
            begin()->
                hasErrors(2)->
                isError('date_range','/Only one/')->
                isError('kilometers_range','/Only one/')->
            end()->
        Click('Filter',array(
            'chart_filters' => array(
                'vehicles_list' => array($browser->getVehicleId('car-charts-1')),
                'vehicle_display' => 'stacked',
                'categories_list' => array($browser->getIdForCategory('Tax'),$browser->getIdForCategory('Fuel')),
                'category_display' => 'single',
                'range_type' => 'date',
                'date_range' => array('from' => '', 'to' => date('Y-m-d')),
                'kilometers_range' => array('from' => '0', 'to' => ''),
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
                isParameter('module','charts')->
                isParameter('action','index')->
            end()->
        with('response')->
            begin()->
                checkElement('body:contains("Warning")]',false)->
                checkElement('body:contains("Notice")]',false)->
            // two vehicles listed, including archived one
                checkElement('div#charts_filters tr input[name="chart_filters[vehicles_list][]"][checked="checked"]',1)->
                checkElement('div#charts_filters tr input[name="chart_filters[vehicle_display]"][checked="checked"]',1)->
                checkElement('div#charts_filters tr input[name="chart_filters[categories_list][]"][checked="checked"]',2)->
                checkElement('div#charts_filters tr input[name="chart_filters[category_display]"][checked="checked"]',1)->
                checkElement('#filter_values_vehicles_list:contains("'.$browser->getVehicleId('car-charts-1').'")',true)->
                checkElement('#filter_values_vehicle_display:contains("stacked")',true)->
                checkElement('#filter_values_categories_list:contains("'.$browser->getIdForCategory('Tax').', '.$browser->getIdForCategory('Fuel').'")',true)->
                checkElement('#filter_values_category_display:contains("single")',true)->
                checkElement('#filter_values_range_type:contains("date")',true)->
                checkElement('#filter_values_date_range:contains(", '.date('Y-m-d').'")',true)->
                checkElement('#filter_values_kilometers_range:contains("0,")',true)->
                checkElement('#filter_values_chart_name:',false)->
                checkElement('table#query_results tbody tr',1)->
                checkElement('table#filter_values tbody tr',7)->
                checkElement('div.vehicle_statistics',1)->
                checkElement('div.vehicle_statistics div.overall_cost',1)->
                checkElement('div.vehicle_statistics div.traveled_distance',1)->
                checkElement('div.vehicle_statistics div.cost_per_km',1)->
                checkElement('div.vehicle_statistics div.fuel_consumption',1)->
            end()->
         with('doctrine')->
            begin()->
              check('Chart',array('user_id' => $browser->getUserId('user_charts')),0)->
             end()->

        call('/user_charts/charts/filter?_reset','post',array('_with_csrf' => true))->
            with('response')->
                begin()->
                    isRedirected()->
                    isStatusCode(302)->
                    followRedirect()->
                end()->

            with('request')->
                begin()->
                    isParameter('module','charts')->
                    isParameter('action','index')->
                end()->
            with('doctrine')->
            begin()->
                check('Chart',array('user_id' => $browser->getUserId('user_charts')),0)->
            end()->
        with('response')->
            begin()->
                checkElement('body:contains("Warning")]',false)->
                checkElement('body:contains("Notice")]',false)->
            // two vehicles listed, including archived one
                checkElement('table#filter_values:contains("No elements found")]',true)->
                checkElement('table#query_results:contains("No elements found")]',true)->
                checkElement('#charts_filters table tbody tr',7)->
            end()->


        info('  2.3 - Clear filters at logout')->
        logout()->
        with('user')->
            begin()->
                isAuthenticated(false)->
                isAttribute('charge.filters', null, 'admin_module')->
            end()->
        login('user_charts','user')->
        with('user')->
            begin()->
                isAuthenticated(true)->
                isAttribute('charge.filters', null, 'admin_module')->
            end()->


         info('3 - Cost per km')->
         get('/user_charts/charts/cost_per_km')->
         with('request')->begin()->
            isParameter('module', 'charts')->
            isParameter('action', 'costPerKm')->
          end()->
          with('response')->begin()->
            isStatusCode(200)->
            checkElement('body:contains("Warning")]',false)->
            checkElement('body:contains("Notice")]',false)->
            checkElement('img',true)->
            checkElement('#filter_values_vehicle_display:contains("single")]',true)->
            checkElement('#filter_values_category_display:contains("stacked")]',true)->
            checkElement('#filter_values_chart_name:contains("cost_per_km")]',true)->
            checkElement('#filter_values_range_type:contains("distance")]',true)->
            checkElement('table#filter_values tbody tr',4)->
          end()->
          with('doctrine')->
            begin()->
                check('Chart',array('user_id' => $browser->getUserId('user_charts')),1)->
            end()->

        Click('Filter',array(
            'chart_filters' => array(
                'vehicles_list' => array($browser->getVehicleId('car-charts-1')),
                'vehicle_display' => 'stacked',
                'categories_list' => array($browser->getIdForCategory('Tax'),$browser->getIdForCategory('Fuel')),
                'category_display' => 'single',
                'range_type' => 'date',
                'date_range' => array('from' => '', 'to' => date('Y-m-d')),
                'kilometers_range' => array('from' => '0', 'to' => ''),
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
                isParameter('module','charts')->
                isParameter('action','costPerKm')->
            end()->
        with('response')->begin()->
            isStatusCode(200)->
            checkElement('body:contains("Warning")]',false)->
            checkElement('body:contains("Notice")]',false)->
            checkElement('img',true)->
            checkElement('#filter_values_vehicles_list:contains("'.$browser->getVehicleId('car-charts-1').'")',true)->
            checkElement('#filter_values_categories_list:contains("'.$browser->getIdForCategory('Tax').', '.$browser->getIdForCategory('Fuel').'")',true)->
            checkElement('#filter_values_date_range:contains(", '.date('Y-m-d').'")',true)->
                checkElement('#filter_values_kilometers_range:contains("0,")',true)->
            checkElement('#filter_values_vehicle_display:contains("stacked")]',true)->
            checkElement('#filter_values_category_display:contains("single")]',true)->
            checkElement('#filter_values_chart_name:contains("cost_per_km")]',true)->
            checkElement('#filter_values_range_type:contains("date")]',true)->
            checkElement('table#filter_values tbody tr',8)->
          end()->
          with('doctrine')->
            begin()->
                check('Chart',array('user_id' => $browser->getUserId('user_charts')),2)->
            end()->
        

        info('4 - Cost per year')->
         get('/user_charts/charts/cost_annual')->
         with('request')->begin()->
            isParameter('module', 'charts')->
            isParameter('action', 'costPerYear')->
          end()->
        call('/user_charts/charts/filter?_reset','post',array('_with_csrf' => true))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
            with('response')->
                begin()->
                    checkElement('body:contains("Warning")]',false)->
                    checkElement('body:contains("Notice")]',false)->
                    checkElement('img',true)->
                    checkElement('#filter_values_vehicle_display:contains("single")]',true)->
                    checkElement('#filter_values_category_display:contains("stacked")]',true)->
                    checkElement('#filter_values_chart_name:contains("cost_per_year")]',true)->
                    checkElement('#filter_values_range_type:contains("date")]',true)->
                    checkElement('table#filter_values tbody tr',4)->
                end()->

        info('5 - Cost pie')->
         get('/user_charts/charts/cost_allocation')->
         with('request')->begin()->
            isParameter('module', 'charts')->
            isParameter('action', 'costPie')->
          end()->
        call('/user_charts/charts/filter?_reset','post',array('_with_csrf' => true))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
            with('response')->
                begin()->
                    checkElement('body:contains("Warning")]',false)->
                    checkElement('body:contains("Notice")]',false)->
                    checkElement('img',true)->
                    checkElement('#filter_values_vehicle_display:contains("single")]',true)->
                    checkElement('#filter_values_chart_name:contains("cost_pie")]',true)->
                    checkElement('table#filter_values tbody tr',2)->
                end()->

        info('6 - Annual travel')->
         get('/user_charts/charts/travel_annual')->
         with('request')->begin()->
            isParameter('module', 'charts')->
            isParameter('action', 'tripAnnual')->
          end()->
        call('/user_charts/charts/filter?_reset','post',array('_with_csrf' => true))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
            with('response')->
                begin()->
                    checkElement('body:contains("Warning")]',false)->
                    checkElement('body:contains("Notice")]',false)->
                    checkElement('img',true)->
                    checkElement('#filter_values_range_type:contains("date")]',true)->
                    checkElement('#filter_values_chart_name:contains("trip_annual")]',true)->
                    checkElement('table#filter_values tbody tr',2)->
                end()->

        info('7 - Monthly travel')->
         get('/user_charts/charts/travel_monthly')->
         with('request')->begin()->
            isParameter('module', 'charts')->
            isParameter('action', 'tripMonthly')->
          end()->
        call('/user_charts/charts/filter?_reset','post',array('_with_csrf' => true))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
            with('response')->
                begin()->
                    checkElement('body:contains("Warning")]',false)->
                    checkElement('body:contains("Notice")]',false)->
                    checkElement('img',true)->
                    checkElement('#filter_values_range_type:contains("date")]',true)->
                    checkElement('#filter_values_chart_name:contains("trip_monthly")]',true)->
                    checkElement('table#filter_values tbody tr',2)->
                end()
          
;


