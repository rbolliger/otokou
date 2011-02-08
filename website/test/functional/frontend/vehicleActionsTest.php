<?php

include(dirname(__FILE__) . '/../../bootstrap/functional.php');

$browser = new otokouTestFunctional(new sfBrowser());
$browser->loadData();
$browser->setTester('doctrine', 'sfTesterDoctrine');

$browser->
        login('user_vehicle','user')->
        info('1 - List')->
        
        info('  1.1 - Routing')->
        get('/settings/vehicles')->
            with('response')->
                isStatusCode(404)->
        get('/user2/settings/vehicles')->
            with('response')->
                isStatusCode(403)->
        get('/user_vehicle/settings/vehicles')->
            with('response')->
                isStatusCode(200)->
            with('request')->
                begin()->
                    isParameter('module','vehicle')->
                    isParameter('action','index')->
                    isParameter('username','user_vehicle')->
                end()->
        
        info('  1.2 - Page elements')->
            with('response')->
                begin()->
                    checkElement('div.sf_admin_filter',false)->
                    checkElement('li.sf_admin_action_archive',2)->
        
                end()->
        
        info('  1.3 - Archive action')->
        with('response')->
                begin()->
                    checkElement('div.sf_admin_list tbody tr',2)->
                    checkElement('div.sf_admin_list tbody tr td.sf_admin_list_td_is_archived img[alt="Checked"]',1)->
                end()->
        get(sprintf('/user_vehicle/settings/vehicles/%s/archive',$browser->getVehicleId('car-vehicle-1')))->
        with('response')->
            begin()->
                isRedirected()->
                followRedirect()->
            end()->
        with('response')->
                begin()->
                    checkElement('div.sf_admin_list tbody tr',2)->
                    checkElement('div.sf_admin_list tbody tr td.sf_admin_list_td_is_archived img[alt="Checked"]',2)->
                end()->
        get(sprintf('/user_vehicle/settings/vehicles/%s/archive',$browser->getVehicleId('car-vehicle-2')))->
        with('response')->
            begin()->
                isRedirected()->
                followRedirect()->
            end()->
        with('response')->
                begin()->
                    checkElement('div.sf_admin_list tbody tr',2)->
                    checkElement('div.sf_admin_list tbody tr td.sf_admin_list_td_is_archived img[alt="Checked"]',1)->
                end()->

        

        info('2 - New')->
        
        info('  2.1 - Routing')->
        get('/settings/vehicles/new')->
            with('response')->
                isStatusCode(404)->
        get('/user2/settings/vehicles/new')->
            with('response')->
                isStatusCode(403)->
        get('/user_vehicle/settings/vehicles/new')->
            with('response')->
                isStatusCode(200)->
            with('request')->
                begin()->
                    isParameter('module','vehicle')->
                    isParameter('action','new')->
                    isParameter('username','user_vehicle')->
                end()->
        
        info('  2.2 - Form submission')->
        click('Save')->
            with('form')->
                begin()->
                    hasErrors(1)->
                    isError('name','/required/')->
                end()->
        click('Save',array('vehicle' => array('name' => 'myCar')))->
            with('form')->
                begin()->
                    hasErrors(false)->
                end()->
            with('doctrine')->
                begin()->
                    check('Vehicle', array(
                          'id'     => $browser->getVehicleId('mycar'),
                        ),true)->
                end()->

        
        
        info('3 - Edit')->
        
        info('  3.1 - Routing')->
        get(sprintf('/settings/vehicles/%s/edit',$browser->getVehicleId('myCar')))->
            with('response')->
                isStatusCode(404)->
        get(sprintf('/user2/settings/vehicles/%s/edit',$browser->getVehicleId('myCar')))->
            with('response')->
                isStatusCode(403)->
        get(sprintf('/user2/settings/vehicles/%s/edit',$browser->getVehicleId('car2')))->
            with('response')->
                isStatusCode(403)->
        get(sprintf('/user_vehicle/settings/vehicles/%s/edit',$browser->getVehicleId('myCar')))->
            with('response')->
                isStatusCode(200)->
            with('request')->
                begin()->
                    isParameter('module','vehicle')->
                    isParameter('action','edit')->
                    isParameter('username','user_vehicle')->
                    isParameter('id',$browser->getVehicleId('myCar'))->
                end()->
        
        
        
        info('4 - Delete')->
        call(sprintf('/user_vehicle/settings/vehicles/%s',$browser->getVehicleId('mycar')), 'delete',array('_with_csrf' => true))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
            with('response')->
                    begin()->
                        checkElement('div.sf_admin_list tbody tr',2)->
                    end()->
            with('doctrine')->
                    begin()->
                        check('Vehicle', array(
                              'id'     => $browser->getVehicleId('mycar'),
                            ),false)->
                    end()
        


;