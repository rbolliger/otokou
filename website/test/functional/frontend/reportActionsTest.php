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
            ->checkElement('h2:contains("Custom reports")',true)
            ->checkElement('ul.custom_reports_menu li.vehicle_active',1)
            ->checkElement('h2:contains("Create a new report")',true)
            ->checkElement('ul.report_new li.vehicle_active',1)
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
            ->checkElement('h2:contains("Custom reports")',true)
            ->checkElement('ul.custom_reports_menu li.vehicle_active',1)
            ->checkElement('body:contains("No new reports available")',false)
            ->checkElement('h1:contains("New reports available")',true)
            ->checkElement('table.reports_list',true)
            ->checkElement('table.reports_list tbody tr',2)
        ->end()


->info('3 - List of reports')
->info('3.1 - Vehicles')
    ->click('car_gs_2')
        ->with('request')
            ->begin()
                ->isParameter('module', 'report')
                ->isParameter('action', 'listVehicle')
            ->end()
        ->with('response')
            ->begin()
            ->isStatusCode(200)
            ->checkElement('h2:contains("Vehicles")',true)
            ->checkElement('ul.vehicles_menu li.vehicle_archived',1)
            ->checkElement('ul.vehicles_menu li.vehicle_active',1)
            ->checkElement('h2:contains("Custom reports")',true)
            ->checkElement('ul.custom_reports_menu li.vehicle_active',1)
            ->checkElement('body:contains("No reports available")',true)
            ->checkElement('table.reports_list',false)
        ->end()
    ->click('car_gs_1')
        ->with('request')
            ->begin()
                ->isParameter('module', 'report')
                ->isParameter('action', 'listVehicle')
            ->end()
        ->with('response')
            ->begin()
            ->isStatusCode(200)
            ->checkElement('h2:contains("Vehicles")',true)
            ->checkElement('ul.vehicles_menu li.vehicle_archived',1)
            ->checkElement('ul.vehicles_menu li.vehicle_active',1)
            ->checkElement('h2:contains("Custom reports")',true)
            ->checkElement('ul.custom_reports_menu li.vehicle_active',1)
            ->checkElement('body:contains("No reports available")',false)
            ->checkElement('table.reports_list',true)
            ->checkElement('table.reports_list tbody tr',2)
            ->checkElement('table.reports_list tbody tr.report_new',1)
            ->checkElement('table.reports_list tbody tr.report_old',1)
        ->end()
->info('3.2 - Custom reports')
    ->click('Custom reports')
        ->with('request')
            ->begin()
                ->isParameter('module', 'report')
                ->isParameter('action', 'listCustom')
            ->end()
        ->with('response')
            ->begin()
            ->isStatusCode(200)
            ->checkElement('h2:contains("Vehicles")',true)
            ->checkElement('ul.vehicles_menu li.vehicle_archived',1)
            ->checkElement('ul.vehicles_menu li.vehicle_active',1)
            ->checkElement('h2:contains("Custom reports")',true)
            ->checkElement('ul.custom_reports_menu li.vehicle_active',1)
            ->checkElement('body:contains("No reports available")',false)
            ->checkElement('table.reports_list',true)
            ->checkElement('table.reports_list tbody tr',1)
            ->checkElement('table.reports_list tbody tr.report_new',1)
            ->checkElement('table.reports_list tbody tr.report_old',0)
        ->end()


->info('4 - Creation of a new custom report')
        ->get('/user_gs/report/new')
            ->with('request')
            ->begin()
                ->isParameter('module', 'report')
                ->isParameter('action', 'new')
            ->end()
            ->with('response')
                ->begin()
                    ->isStatusCode(200)
                    ->checkElement('h2:contains("Vehicles")',true)
                    ->checkElement('ul.vehicles_menu li.vehicle_archived',1)
                    ->checkElement('ul.vehicles_menu li.vehicle_active',1)
                    ->checkElement('h2:contains("Custom reports")',true)
                    ->checkElement('ul.custom_reports_menu li.vehicle_active',1)
                    ->checkElement('h1:contains("Create a new custom report")',true)
                    ->checkElement('div.report_form form',true)
                    ->checkElement('div.report_form form table tbody tr',4)
                ->end()
       ->click('Create', array())
                ->with('form')
                    ->begin()
                        ->hasErrors(2)
                        ->isError('name', '/required/')
                        ->isError('vehicles_list', '/required/')
                    ->end()
                ->with('request')
                    ->begin()
                        ->isParameter('module', 'report')
                        ->isParameter('action', 'create')
                    ->end()
        ->click('Create', array('report' =>
            array(
                'name' => 'Custom report',
                'date_range' => array('from' => date('Y-m-d',time())),
                'kilometers_range' => array('from' => 0),
                'vehicles_list' => array(
                    $browser->getVehicleId('car2')
                )
                )))
                ->with('form')
                    ->begin()
                        ->hasErrors(3)
                        ->isError('date_range', '/Only one/')
                        ->isError('kilometers_range', '/Only one/')
                        ->isError('vehicles_list', '/invalid/')
                    ->end()
        ->click('Create', array('report' =>
            array(
                'name' => 'Custom report',
                'date_range' => array('from' => date('Y-m-d'), 'to' => ''),
                'kilometers_range' => array('from' => '', 'to' => 10000),
                'vehicles_list' => array(
                    $browser->getVehicleId('car-gs-1')
                        )
                    )))
                ->with('form')
                    ->begin()
                        ->hasErrors(false)
                    ->end()
                ->with('request')
                    ->begin()
                        ->isParameter('module', 'report')
                        ->isParameter('action', 'create')
                    ->end()
                ->with('doctrine')
                    ->begin()
                        ->check('Report',
                                Doctrine_Core::getTable('Report')->createQuery('r')
                                    ->andWhere('r.name LIKE ?','Custom report')
                                    ->andWhere('r.date_from = ?',date('Y-m-d'))
                                    ->andWhere('kilometers_to = ?', 10000)
                                    ->leftJoin('r.Vehicles v')
                                    ->andWhereIn('v.id',array($browser->getVehicleId('car-gs-1')))
                                 ,1)
                    ->end()
                ->with('response')
                    ->begin()
                        ->isRedirected(true)
                        ->followRedirect()
                    ->end()
                ->with('request')
                    ->begin()
                        ->isParameter('module', 'report')
                        ->isParameter('action', 'show')
                    ->end()
                ->with('response')
                    ->begin()
                        ->isStatusCode(200)
                    ->end()
        ->get('/user_gs/report/new')
        ->click('Create', array('report' =>
            array(
                'name' => 'Custom report',
                'vehicles_list' => array(
                    $browser->getVehicleId('car-gs-1')
                )
             )))  // testing which defaults values are set in "from" and "to" ranges
                ->with('form')
                    ->begin()
                        ->hasErrors(false)
                    ->end()
                ->with('response')
                    ->begin()
                        ->isRedirected(true)
                        ->followRedirect()
                    ->end()
                ->with('doctrine')
                    ->begin()
                        ->check('Report',
                                Doctrine_Core::getTable('Report')->createQuery('r')
                                    ->andWhere('r.name LIKE ?','Custom report')
                                    ->andWhere('r.date_to = ?',date('Y-m-d'))
                                    ->andWhere('kilometers_from = ?', 0)
                                    ->leftJoin('r.Vehicles v')
                                    ->andWhereIn('v.id',array($browser->getVehicleId('car-gs-1')))
                                ,1)
                    ->end()

->info('5 - Delete')
        ->info('5.1 - Vehicle report')
        ->get('/user_gs/report/new')
        ->click('Create', array('report' =>
            array(
                'name' => 'Custom report delete vehicle 1',
                'date_range' => array('from' => date('Y-m-d'), 'to' => ''),
                'kilometers_range' => array('from' => '', 'to' => 10000),
                'vehicles_list' => array(
                    $browser->getVehicleId('car-gs-1')
                        )
                    )))
         ->get('/user_gs/reports/vehicle/car-gs-1')
             ->with('response')
                ->begin()
                      ->checkElement('table.reports_list tbody tr',5)
                      ->checkElement('table.reports_list tbody tr td:contains("Custom report delete vehicle 1")',true)
                ->end()
             ->with('doctrine')
                    ->begin()
                        ->check('Report',
                                Doctrine_Core::getTable('Report')->createQuery('r')
                                    ->andWhere('r.name LIKE ?','Custom report delete vehicle 1')
                                    ->andWhere('r.date_from = ?',date('Y-m-d'))
                                    ->andWhere('kilometers_to = ?', 10000)
                                    ->leftJoin('r.Vehicles v')
                                    ->andWhereIn('v.id',array($browser->getVehicleId('car-gs-1')))
                                 ,1)
                    ->end()
        ->call('/user_gs/report/delete/custom-report-delete-vehicle-1','delete',array('_with_csrf' => true))
            ->with('request')
                 ->begin()
                     ->isParameter('module', 'report')
                     ->isParameter('action', 'delete')
                 ->end()
            ->with('response')
                ->begin()
                     ->isRedirected(true)
                     ->followRedirect()
                ->end()
            ->with('request')
                ->begin()
                     ->isParameter('module', 'report')
                     ->isParameter('action', 'listVehicle')
                 ->end()
            ->with('response')
                ->begin()
                    ->checkElement('table.reports_list tbody tr',4)
                    ->checkElement('table.reports_list tbody tr td:contains("Custom report delete vehicle 1")',false)
                ->end()
             ->with('doctrine')
                    ->begin()
                        ->check('Report',
                                Doctrine_Core::getTable('Report')->createQuery('r')
                                    ->andWhere('r.name LIKE ?','Custom report delete vehicle 1')
                                    ->andWhere('r.date_from = ?',date('Y-m-d'))
                                    ->andWhere('kilometers_to = ?', 10000)
                                    ->leftJoin('r.Vehicles v')
                                    ->andWhereIn('v.id',array($browser->getVehicleId('car-gs-1')))
                                 ,false)
                    ->end()

        ->info('5.2 - Custom report')
        ->get('/user_gs/report/new')
        ->click('Create', array('report' =>
            array(
                'name' => 'Custom report delete vehicle 2',
                'date_range' => array('from' => date('Y-m-d'), 'to' => ''),
                'kilometers_range' => array('from' => '', 'to' => 10000),
                'vehicles_list' => array(
                    $browser->getVehicleId('car-gs-1'),
                    $browser->getVehicleId('car-gs-2'),
                        )
                    )))
         ->get('/user_gs/reports/custom')
             ->with('response')
                ->begin()
                      ->checkElement('table.reports_list tbody tr',2)
                      ->checkElement('table.reports_list tbody tr td:contains("Custom report delete vehicle 2")',true)
                ->end()
             ->with('doctrine')
                    ->begin()
                        ->check('Report',
                                Doctrine_Core::getTable('Report')->createQuery('r')
                                    ->andWhere('r.name LIKE ?','Custom report delete vehicle 2')
                                    ->andWhere('r.date_from = ?',date('Y-m-d'))
                                    ->andWhere('kilometers_to = ?', 10000)
                                    ->leftJoin('r.Vehicles v')
                                    ->andWhereIn('v.id',array($browser->getVehicleId('car-gs-1'),$browser->getVehicleId('car-gs-2')))
                                 ,1)
                    ->end()
        ->call('/user_gs/report/delete/custom-report-delete-vehicle-2','delete',array('_with_csrf' => true))
            ->with('request')
                 ->begin()
                     ->isParameter('module', 'report')
                     ->isParameter('action', 'delete')
                 ->end()
            ->with('response')
                ->begin()
                     ->isRedirected(true)
                     ->followRedirect()
                ->end()
            ->with('request')
                ->begin()
                     ->isParameter('module', 'report')
                     ->isParameter('action', 'listCustom')
                 ->end()
            ->with('response')
                ->begin()
                    ->checkElement('table.reports_list tbody tr',1)
                    ->checkElement('table.reports_list tbody tr td:contains("Custom report delete vehicle 2")',false)
                ->end()
             ->with('doctrine')
                    ->begin()
                        ->check('Report',
                                Doctrine_Core::getTable('Report')->createQuery('r')
                                    ->andWhere('r.name LIKE ?','Custom report delete vehicle 2')
                                    ->andWhere('r.date_from = ?',date('Y-m-d'))
                                    ->andWhere('kilometers_to = ?', 10000)
                                    ->leftJoin('r.Vehicles v')
                                    ->andWhereIn('v.id',array($browser->getVehicleId('car-gs-1'),$browser->getVehicleId('car-gs-2')))
                                 ,false)
                    ->end()
        ;
