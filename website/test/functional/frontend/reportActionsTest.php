<?php

include(dirname(__FILE__) . '/../../bootstrap/functional.php');
include dirname(__FILE__) . '/../../../lib/test/otokouTestFunctional.class.php';

// creating temporary folder
$pdf_dir = sfConfig::get('sf_web_dir') . '/functional/pdf';

$fs = new sfFilesystem();
$fs->mkdirs($pdf_dir);


$browser = new otokouTestFunctional(new sfBrowser());
$browser->loadData();
$browser->setTester('doctrine', 'sfTesterDoctrine');

$browser
        ->info('1 - Security')
        ->info('1.1 - No access without login')
        ->get('/ruf/reports')
        ->with('request')
        ->begin()
        ->isParameter('module', 'report')
        ->isParameter('action', 'index')
        ->end()
        ->with('response')
        ->begin()
        ->isStatusCode(401)
        ->end()
        ->info('1.2 - Username is required in route')
        ->login('user_gs', 'user')
        ->get('/reports')
        ->with('response')
        ->begin()
        ->isStatusCode(404)
        ->end()
        ->info('1.3 - A user can only access his resources')
        ->get('/ruf/reports')
        ->with('response')
        ->begin()
        ->isStatusCode(403)
        ->end()
        ->info('Correct request')
        ->get('/user_gs/reports')
        ->with('request')
        ->begin()
        ->isParameter('module', 'report')
        ->isParameter('action', 'index')
        ->end()
        ->logout()
        ->info('2 - Index')
        ->info('2.1 - A user only sees his reports')
        ->login('ruf', 'admin@1')
        ->get('/ruf/reports')
        ->with('response')
        ->begin()
        ->isStatusCode(200)
        ->checkElement('ul li a:contains("By Vehicle")', true)
        ->checkElement('ul#reports_menu ul li.vehicle_archived', 0)
        ->checkElement('ul#reports_menu ul li.vehicle_active', 1)
        ->checkElement('ul#reports_menu li a:contains("Custom reports")', true)
        ->checkElement('ul#reports_menu li a:contains("Create a new report")', true)
        ->checkElement('h3.report_category_title', 0)
        ->checkElement('body:contains("No reports available.")', true)
        ->end()
        ->logout()
        ->login('user_gs', 'user')
        ->get('/user_gs/reports')
        ->with('response')
        ->begin()
        ->isStatusCode(200)
        ->checkElement('ul li a:contains("By Vehicle")', true)
        ->checkElement('ul#reports_menu ul li.vehicle_archived', 1)
        ->checkElement('ul#reports_menu ul li.vehicle_active', 2)
        ->checkElement('ul#reports_menu li a:contains("Custom reports")', true)
        ->checkElement('body:contains("No reports available")', false)
        ->checkElement('h1:contains("Reports")', true)
        ->checkElement('h3.report_category_title', 2)
        ->checkElement('h3.report_category_title:contains("car_gs_1")', 1)
        ->checkElement('div#category_car-gs-1 table.reports_list tbody tr', 2)
        ->checkElement('h3.report_category_title:contains("Custom reports")', 1)
        ->checkElement('div#category_custom_reports table.reports_list tbody tr', 1)
        ->checkElement('table.reports_list', 2)
        ->checkElement('table.reports_list tbody tr', 3)
        ->checkElement('tfoot.more_reports', false)
        ->end()
        ->logout()
        ->login('user_reports', 'user')
        ->get('/user_reports/reports')
        ->with('response')
        ->begin()
        ->isStatusCode(200)
        ->checkElement('ul li a:contains("By Vehicle")', true)
        ->checkElement('ul#reports_menu ul li.vehicle_archived', 1)
        ->checkElement('ul#reports_menu ul li.vehicle_active', 1)
        ->checkElement('ul#reports_menu li a:contains("Custom reports")', true)
        ->checkElement('body:contains("No reports available")', false)
        ->checkElement('h1:contains("Reports")', true)
        ->checkElement('h3.report_category_title:contains("car_reports_1")', 1)
        ->checkElement('div#category_car-reports-1 table.reports_list tbody tr', 3)
        ->checkElement('table.reports_list', 1)
        ->checkElement('table.reports_list tbody tr', 3)
        ->checkElement('tfoot.more_reports', 1)
        ->end()
        ->logout()
        ->info('3 - List of reports')
        ->login('user_gs', 'user')
        ->get('/user_gs/reports')
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
        ->checkElement('ul li a:contains("By Vehicle")', true)
        ->checkElement('ul#reports_menu ul li.vehicle_archived', 1)
        ->checkElement('ul#reports_menu ul li.vehicle_active', 2)
        ->checkElement('ul#reports_menu li a:contains("Custom reports")', true)
        ->checkElement('body:contains("No reports available")', true)
        ->checkElement('table.reports_list', false)
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
        ->checkElement('ul li a:contains("By Vehicle")', true)
        ->checkElement('ul#reports_menu ul li.vehicle_archived', 1)
        ->checkElement('ul#reports_menu ul li.vehicle_active', 2)
        ->checkElement('ul#reports_menu li a:contains("Custom reports")', true)
        ->checkElement('body:contains("No reports available")', false)
        ->checkElement('table.reports_list', true)
        ->checkElement('table.reports_list tbody tr', 2)
        ->checkElement('table.reports_list tbody tr.report_new', 1)
        ->checkElement('table.reports_list tbody tr.report_old', 1)
        ->end()
        ->logout()
        ->login('user_reports', 'user')
        ->get('/user_reports/reports/vehicle/car-reports-1')
        ->with('request')
        ->begin()
        ->isParameter('module', 'report')
        ->isParameter('action', 'listVehicle')
        ->isParameter('username', 'user_reports')
        ->isParameter('slug', 'car-reports-1')
        ->end()
        ->with('response')
        ->begin()
        ->isStatusCode(200)
        ->checkElement('ul li a:contains("By Vehicle")', true)
        ->checkElement('ul#reports_menu ul li.vehicle_archived', 1)
        ->checkElement('ul#reports_menu ul li.vehicle_active', 1)
        ->checkElement('ul#reports_menu li a:contains("Custom reports")', true)
        ->checkElement('body:contains("No reports available")', false)
        ->checkElement('table.reports_list', true)
        ->checkElement('table.reports_list tbody tr', 20)
        ->checkElement('table.reports_list tbody tr.report_new', 15)
        ->checkElement('table.reports_list tbody tr.report_old', 5)
        ->checkElement('div.pagination', true)
        ->end()
        ->info('3.2 - Custom reports')
        ->logout()
        ->login('user_gs', 'user')
        ->get('/user_gs/reports/custom')
        ->with('request')
        ->begin()
        ->isParameter('module', 'report')
        ->isParameter('action', 'listCustom')
        ->end()
        ->with('response')
        ->begin()
        ->isStatusCode(200)
        ->checkElement('ul li a:contains("By Vehicle")', true)
        ->checkElement('ul#reports_menu ul li.vehicle_archived', 1)
        ->checkElement('ul#reports_menu ul li.vehicle_active', 2)
        ->checkElement('ul#reports_menu li a:contains("Custom reports")', true)
        ->checkElement('body:contains("No reports available")', false)
        ->checkElement('table.reports_list', true)
        ->checkElement('table.reports_list tbody tr', 1)
        ->checkElement('table.reports_list tbody tr.report_new', 1)
        ->checkElement('table.reports_list tbody tr.report_old', 0)
        ->checkElement('div.pagination', false)
        ->end()
        ->info('4 - Creation of a new custom report')
        ->info('4.1 - "New" form')
        ->get('/user_gs/report/new')
        ->with('request')
        ->begin()
        ->isParameter('module', 'report')
        ->isParameter('action', 'new')
        ->end()
        ->with('response')
        ->begin()
        ->isStatusCode(200)
        ->checkElement('ul li a:contains("By Vehicle")', true)
        ->checkElement('ul#reports_menu ul li.vehicle_archived', 1)
        ->checkElement('ul#reports_menu ul li.vehicle_active', 2)
        ->checkElement('ul#reports_menu li a:contains("Custom reports")', true)
        ->checkElement('h1:contains("Create a new custom report")', true)
        ->checkElement('div.report_form form', true)
        ->checkElement('div.report_form form table tbody tr', 4)
        ->end()
        ->info('4.2 - Form errors - required fields')
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
        ->info('4.3 - Form errors - ranges and vehicles list')
        ->click('Create', array('report' =>
            array(
                'name' => 'Custom report',
                'date_range' => array('from' => date('Y-m-d', time())),
                'kilometers_range' => array('from' => 0),
                'vehicles_list' => array(
                    $browser->getVehicleId('car2')
                )
                )))
        ->with('form')
        ->begin()
        ->hasErrors(2)
        ->hasGlobalError('/Only one/')
        ->isError('vehicles_list', '/invalid/')
        ->end()
        ->info('4.4 - No charges related to the defined range')
        ->click('Create', array('report' =>
            array(
                'name' => 'Custom report',
                'date_range' => array('from' => null, 'to' => null),
                'kilometers_range' => array('from' => 4, 'to' => 5),
                'vehicles_list' => array(
                    $browser->getVehicleId('car-gs-1')
                )
                )))
        ->with('form')
        ->begin()
        ->hasErrors(1)
        ->hasGlobalError('/No charges/')
        ->end()
        ->info('4.5 - Form errors - user_id')
        ->click('Create', array('report' =>
            array(
                'name' => 'Custom report',
                'user_id' => $browser->getUserId('ruf'),
                'date_range' => array('from' => null, 'to' => date('Y-m-d')),
                'kilometers_range' => array('from' => 0, 'to' => null),
                'vehicles_list' => array(
                    $browser->getVehicleId('car-gs-1')
                )
                )))
        ->with('form')
        ->begin()
        ->hasErrors(1)
        ->hasGlobalError('/User/')
        ->end()
        ->info('4.6 - Form ok')
        ->click('Create', array('report' =>
            array(
                'name' => 'Custom report',
                'user_id' => $browser->getUserId('user_gs'),
                'date_range' => array('from' => '', 'to' => date('Y-m-d')),
                'kilometers_range' => array('from' => 220, 'to' => ''),
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
        ->check('Report', Doctrine_Core::getTable('Report')
                ->createQuery('r')
                ->andWhere('r.name LIKE ?', 'Custom report')
                ->andWhere('r.date_to = ?', date('Y-m-d'))
                ->andWhere('kilometers_from = ?', 220)
                ->leftJoin('r.Vehicles v')
                ->andWhereIn('v.id', array($browser->getVehicleId('car-gs-1')))
                , 1)
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
        ->info('4.7 - Default values for form')
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
        ->check('Report', Doctrine_Core::getTable('Report')
                ->createQuery('r')
                ->andWhere('r.name LIKE ?', 'Custom report')
                ->andWhere('r.date_to = ?', date('Y-m-d'))
                ->andWhere('kilometers_from = ?', 0)
                ->leftJoin('r.Vehicles v')
                ->andWhereIn('v.id', array($browser->getVehicleId('car-gs-1')))
                , 1)
        ->end()
        ->info('5 - Delete')
        ->info('5.1 - Vehicle report')
        ->get('/user_gs/report/new')
        ->click('Create', array('report' =>
            array(
                'name' => 'Custom report delete vehicle 1',
                'date_range' => array('from' => '', 'to' => date('Y-m-d')),
                'kilometers_range' => array('from' => 30, 'to' => ''),
                'vehicles_list' => array(
                    $browser->getVehicleId('car-gs-1')
                )
                )))
        ->get('/user_gs/reports/vehicle/car-gs-1')
        ->with('response')
        ->begin()
        ->checkElement('table.reports_list tbody tr', 5)
        ->checkElement('table.reports_list tbody tr td:contains("Custom report delete vehicle 1")', true)
        ->end()
        ->with('doctrine')
        ->begin()
        ->check('Report', Doctrine_Core::getTable('Report')
                ->createQuery('r')
                ->andWhere('r.name LIKE ?', 'Custom report delete vehicle 1')
                ->andWhere('r.date_to = ?', date('Y-m-d'))
                ->andWhere('kilometers_from = ?', 30)
                ->leftJoin('r.Vehicles v')
                ->andWhereIn('v.id', array($browser->getVehicleId('car-gs-1')))
                , 1)
        ->end()
        ->call('/user_gs/report/delete/custom-report-delete-vehicle-1', 'delete', array('_with_csrf' => true))
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
        ->checkElement('table.reports_list tbody tr', 4)
        ->checkElement('table.reports_list tbody tr td:contains("Custom report delete vehicle 1")', false)
        ->end()
        ->with('doctrine')
        ->begin()
        ->check('Report', Doctrine_Core::getTable('Report')
                ->createQuery('r')
                ->andWhere('r.name LIKE ?', 'Custom report delete vehicle 1')
                ->andWhere('r.date_from = ?', date('Y-m-d'))
                ->andWhere('kilometers_to = ?', 10000)
                ->leftJoin('r.Vehicles v')
                ->andWhereIn('v.id', array($browser->getVehicleId('car-gs-1')))
                , false)
        ->end()
        ->info('5.2 - Custom report')
        ->get('/user_gs/report/new')
        ->click('Create', array('report' =>
            array(
                'name' => 'Custom report delete vehicle 2',
                'date_range' => array('from' => '', 'to' => date('Y-m-d')),
                'kilometers_range' => array('from' => 300, 'to' => ''),
                'vehicles_list' => array(
                    $browser->getVehicleId('car-gs-1'),
                    $browser->getVehicleId('car-gs-2'),
                )
                )))
        ->get('/user_gs/reports/custom')
        ->with('response')
        ->begin()
        ->checkElement('table.reports_list tbody tr', 2)
        ->checkElement('table.reports_list tbody tr td:contains("Custom report delete vehicle 2")', true)
        ->end()
        ->with('doctrine')
        ->begin()
        ->check('Report', Doctrine_Core::getTable('Report')
                ->createQuery('r')
                ->andWhere('r.name LIKE ?', 'Custom report delete vehicle 2')
                ->andWhere('r.date_to = ?', date('Y-m-d'))
                ->andWhere('kilometers_from = ?', 300)
                ->leftJoin('r.Vehicles v')
                ->andWhereIn('v.id', array($browser->getVehicleId('car-gs-1'), $browser->getVehicleId('car-gs-2')))
                , 1)
        ->end()
        ->call('/user_gs/report/delete/custom-report-delete-vehicle-2', 'delete', array('_with_csrf' => true))
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
        ->checkElement('table.reports_list tbody tr', 1)
        ->checkElement('table.reports_list tbody tr td:contains("Custom report delete vehicle 2")', false)
        ->end()
        ->with('doctrine')
        ->begin()
        ->check('Report', Doctrine_Core::getTable('Report')
                ->createQuery('r')
                ->andWhere('r.name LIKE ?', 'Custom report delete vehicle 2')
                ->andWhere('r.date_from = ?', date('Y-m-d'))
                ->andWhere('kilometers_to = ?', 10000)
                ->leftJoin('r.Vehicles v')
                ->andWhereIn('v.id', array($browser->getVehicleId('car-gs-1'), $browser->getVehicleId('car-gs-2')))
                , false)
        ->end()
        ->info('6 - Show action')
        ->info('6.1 - isNew() flag')
        ->get('/user_gs/reports')
        ->with('doctrine')
        ->begin()
        ->check('Report', Doctrine_Core::getTable('Report')
                ->createQuery('r')
                ->andWhere('r.user_id = ?', $browser->getUserId('user_gs'))
                , 5)
        ->check('Report', Doctrine_Core::getTable('Report')
                ->createQuery('r')
                ->andWhere('r.user_id = ?', $browser->getUserId('user_gs'))
                ->andWhere('r.is_new = ?', true)
                , 2)
        ->end()
        ->with('response')
        ->begin()
        ->checkElement('table.reports_list', 2)
        ->checkElement('table.reports_list tbody tr', 4)
        ->checkElement('table.reports_list tbody tr.report_new', 2)
        ->checkElement('tfoot.more_reports', 1)
        ->end()
        ->click('0-100 km - Car gs 1')
        ->with('request')
        ->begin()
        ->isParameter('module', 'report')
        ->isParameter('action', 'show')
        ->end()
        ->get('/user_gs/reports')
        ->with('response')
        ->begin()
        ->checkElement('table.reports_list', true)
        ->checkElement('table.reports_list tbody tr', 4)
        ->checkElement('table.reports_list tbody tr.report_new', 1)
        ->end()
        ->with('doctrine')
        ->begin()
        ->check('Report', Doctrine_Core::getTable('Report')
                ->createQuery('r')
                ->andWhere('r.user_id = ?', $browser->getUserId('user_gs'))
                ->andWhere('r.is_new = ?', true)
                , 1)
        ->end()
        ->info('6.2 - Single vehicle')
        ->get('/user_gs/report/show/0-100-km-car-gs-1')
        ->with('request')
        ->begin()
        ->isParameter('module', 'report')
        ->isParameter('action', 'show')
        ->end()
        ->with('response')
        ->begin()
        ->isStatusCode(200)
        ->checkElement('h2:contains("Download")', true)
        ->checkElement('div.report_download a:contains("pdf")')
        ->checkElement('ul li a:contains("By Vehicle")', true)
        ->checkElement('ul#reports_menu ul li.vehicle_archived', 1)
        ->checkElement('ul#reports_menu ul li.vehicle_active', 2)
        ->checkElement('ul#reports_menu li a:contains("Custom reports")', true)
        ->checkElement('ul li a:contains("Create a new report")', true)
        ->checkElement('h1:contains("Overall performances")', 1)
        ->checkElement('h2:contains("car_gs")', 1)
        ->checkElement('h1:contains("Costs")', 1)
        ->checkElement('h2:contains("Cost per kilometer")', 1)
        ->checkElement('h2:contains("Annual cost")', 1)
        ->checkElement('h2:contains("Costs allocation")', 1)
        ->checkElement('h1:contains("Travel")', 1)
        ->checkElement('h2:contains("Annual travel")', 1)
        ->checkElement('h2:contains("Monthly travel")', 1)
        ->checkElement('h1:contains("Fuel consumption")', 1)
        ->checkElement('body div#main img', 6)
        ->end()
        ->info('6.3 - Multiple vehicles')
        ->get('/user_gs/report/show/0-1000-km-car-gs-1-and-car-gs-2')
        ->with('request')
        ->begin()
        ->isParameter('module', 'report')
        ->isParameter('action', 'show')
        ->end()
        ->with('response')
        ->begin()
        ->isStatusCode(200)
        ->checkElement('h2:contains("Download")', true)
        ->checkElement('div.report_download a:contains("pdf")')
        ->checkElement('ul li a:contains("By Vehicle")', true)
        ->checkElement('ul#reports_menu ul li.vehicle_archived', 1)
        ->checkElement('ul#reports_menu ul li.vehicle_active', 2)
        ->checkElement('ul#reports_menu li a:contains("Custom reports")', true)
        ->checkElement('ul li a:contains("Create a new report")', true)
        ->checkElement('h1:contains("Overall performances")', 1)
        ->checkElement('h2:contains("car_gs")', 2)
        ->checkElement('h1:contains("Costs")', 1)
        ->checkElement('h2:contains("Cost per kilometer")', 1)
        ->checkElement('h2:contains("Annual cost")', 1)
        ->checkElement('h2:contains("Costs allocation")', 1)
        ->checkElement('h1:contains("Travel")', 1)
        ->checkElement('h2:contains("Annual travel")', 1)
        ->checkElement('h2:contains("Monthly travel")', 1)
        ->checkElement('h1:contains("Fuel consumption")', 1)
        ->checkElement('body div#main img', 6)
        ->checkElement('body:contains("Not enough data")',false)
        ->end()
        ->info('7 - Pdf')
        ->get('/user_gs/report/pdf/0-1000-km-car-gs-1-and-car-gs-2')
        ->with('request')
        ->begin()
        ->isParameter('module', 'report')
        ->isParameter('action', 'pdf')
        ->end()
        ->with('response')
        ->begin()
        ->isStatusCode(200)
        ->isHeader('content-type', 'application/pdf')
;

sfToolkit::clearDirectory($pdf_dir);
rmdir($pdf_dir);