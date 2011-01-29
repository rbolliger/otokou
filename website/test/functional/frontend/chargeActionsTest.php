<?php

include(dirname(__FILE__) . '/../../bootstrap/functional.php');

$browser = new otokouTestFunctional(new sfBrowser());
$browser->loadData()->login();
$browser->setTester('doctrine', 'sfTesterDoctrine');



$fuelId = $browser->getIdForCategory('fuel');



$browser->info('1 - The charge form')->
        
        info('  1.1 - If "fuel" category is selected, a quantity must be specified')->
            get('/charge/new')->
                with('request')->
                    begin()->
                        isParameter('module', 'charge')->
                        isParameter('action', 'new')->
                    end()->
            click('Save', getFormData($browser, array('category_id' => $fuelId, 'quantity' => null)))->
                with('form')->
                    begin()->
                        hasErrors(1)->
                        isError('quantity', '/quantity/')->
                    end()->
            click('Save', getFormData($browser, array('category_id' => $fuelId, 'quantity' => 12)))->
                with('form')->
                    begin()->
                        hasErrors(false)->
                    end()->
        
        info(' 1.2 - If any other category is selected, the quantity cannot be specified')->
            get('/charge/new')->
            click('Save', getFormData($browser, array('category_id' => $browser->getIdForCategory('Tax'), 'quantity' => 12)))->
                with('form')->
                    begin()->
                        hasErrors(1)->
                        isError('quantity', '/quantity/')->
                    end()->
            click('Save', getFormData($browser, array('category_id' => $browser->getIdForCategory('Tax'), 'quantity' => null)))->
                with('form')->begin()->
                    hasErrors(false)->
                end()->
        
        info('  1.3 - The "Save" button redirects to object edit')->
            get('/charge/new')->
            click('Save', getFormData($browser, array('category_id' => $browser->getIdForCategory('Tax'), 'quantity' => null)))->
                with('response')->
                    begin()->
                        isRedirected()->
                        followRedirect()->
                    end()->
                with('request')->
                    begin()->
                        isParameter('module', 'charge')->
                        isParameter('action', 'edit')->
                    end()->
        
        info('  1.4 - The "Save and add" button redirects to a new charge input form')->
            get('/charge/new')->
            click('Save and add', getFormData($browser, array('category_id' => $browser->getIdForCategory('Tax'), 'quantity' => null)))->
                with('response')->
                    begin()->
                        isRedirected()->
                        followRedirect()->
                    end()->
                with('request')->
                    begin()->
                        isParameter('module', 'charge')->
                        isParameter('action', 'new')->
                    end()->
        
        
info('2 - Data access rights')->
            logout()->
            login('user2','user2')->
        
        info('  2.1 - A user can only see his charges in list')->
            get('/charge')->
                with('request')->
                    begin()->
                        isParameter('username','user2')->
                    end()->
                with('response')->
                    begin()->
                        checkElement('div.sf_admin_list tbody tr',1)->
                    end()->
                with('user')->
                    begin()->
                        hasCredential('owner')->
                    end()->
        
        info('  2.2 - A user cannot see other users charges list')->
            get('/ruf/charge')->
                with('request')->
                    begin()->
                        isParameter('username','ruf')->
                    end()->
                with('response')->
                    begin()->
                        isStatusCode(403)->
                    end()->
                with('user')->
                    begin()->
                        hasCredential('owner',false)->
                    end()->
        
        info('  2.3 - A user cannot edit other user\'s charges')->
            get('/ruf/charge/'.
                    $browser->getOneChargeByParams(array('user_id' => $browser->getUserId('ruf')))->getId().'/edit')->
                with('response')->
                    begin()->
                        isStatusCode(403)->
                    end()-> 
                with('user')->
                    begin()->
                        hasCredential('owner',false)->
                    end()->
            get('/user2/charge/'.
                    $browser->getOneChargeByParams(array('user_id' => $browser->getUserId('user2')))->getId().'/edit')->
                with('response')->
                    begin()->
                        isStatusCode(200)->
                    end()-> 
                with('user')->
                    begin()->
                        hasCredential('owner',true)->
                    end()->
        
        info('  2.4 - A user cannot delete other user\'s charges')->
            call('/ruf/charge/'.
                    $browser->getOneChargeByParams(array('user_id' => $browser->getUserId('ruf')))->getId(),
                    'delete',
                    array('_with_csrf' => true))->
                with('response')->
                    begin()->
                        isStatusCode(403)->
                    end()-> 
                with('user')->
                    begin()->
                        hasCredential('owner',false)->
                    end()->
            call('/user2/charge/'.
                    $browser->getOneChargeByParams(array('user_id' => $id = $browser->getUserId('user2')))->getId(),
                    'delete',
                    array('_with_csrf' => true))->
                 with('doctrine')->
                    begin()->
                        check('Charge', array(
                          'id'     => $id,
                        ),false)->
                    end()->
                 with('user')->
                    begin()->
                        hasCredential('owner',true)->
                    end()->

        
        info('  2.5 - A user cannot create charges for other users')->
            get('/ruf/charge/new')->
                with('response')->
                    begin()->
                        isStatusCode(403)->
                    end()->  
                with('user')->
                    begin()->
                        hasCredential('owner',false)->
                    end()->
        
        
info('3 - Vehicle choices')->
        
        info('  3.1 - A user can only see his cars')->
        logout()->
        login('ruf','admin@1')->
        get('/ruf/charge/new')->
        with('response')->
            begin()->
                checkElement('select#charge_vehicle_id option',1)->
            end()->
        
        info('  3.2 - A user cannot select other user\'s vechicles')->
        click('Save', getFormData($browser, array('vehicle_id' => $browser->getVehicleId('car3'))))->
                with('form')->
                    begin()->
                        hasErrors(1)->
                        isError('vehicle_id', '/invalid/')->
                    end()->
        
        info('  3.3 - A user cannot select non-registered vechicles')->
        click('Save', getFormData($browser, array('vehicle_id' => $browser->getVehicleId('car-non-existent'))))->
                with('form')->
                    begin()->
                        hasErrors(1)->
                        isError('vehicle_id', '/required/')->
                    end()->
        
        info('  3.4 - A user can only select his own vechicles')->
        click('Save', getFormData($browser, array('vehicle_id' => $browser->getVehicleId('vw-touran-1-4-tsi'))))->
                with('form')->
                    begin()->
                        hasErrors(false)->
                    end()->
        
info('4 - List filters')->
        logout()->
        login('ruf','admin@1')->
        
        info('  4.1 - Vehicle: the user can see only his vehicles in the list')->
        get('/ruf/charge')->
            with('response')->
                begin()->
                    checkElement('select#charge_filters_vehicle_id option',2)->
                end()->
        
        info('  4.2 - Vehicle: the filtering works')->
        click('Filter',array(
            'charge_filters' => array(
                'vehicle_id' => $browser->getVehicleId('car2')
        )))->
            with('form')->
                begin()->
                    hasErrors(1)->
                    isError('vehicle_id', '/invalid/')->
                end()->
        click('Filter',array(
            'charge_filters' => array(
                'vehicle_id' => $browser->getVehicleId('vw-touran-1-4-tsi'))))->
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
                    checkElement('div.sf_admin_list tbody tr',16)->
                end()->
            with('doctrine')->
                begin()->
                    check('Charge', array(
                          'vehicle_id'     => $browser->getVehicleId('vw-touran-1-4-tsi'),
                        ),16)->
                end()->
        
        
        info('  4.3 - Kilometers (and Amount and quantity): the range filter works')->
        click('Filter',array(
            'charge_filters' => array(
                'kilometers' => array('from' => 0))))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
            with('response')->
                begin()->
                    checkElement('div.sf_admin_list tbody tr',16)->
                end()->
        click('Filter',array(
            'charge_filters' => array(
                'kilometers' => array('from' => 35, 'to' => 500))))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
            with('response')->
                begin()->
                    checkElement('div.sf_admin_list tbody tr',10)->
                end()->
        click('Filter',array(
            'charge_filters' => array(
                'kilometers' => array('from' => null, 'to' => 1500))))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
            with('response')->
                begin()->
                    checkElement('div.sf_admin_list tbody tr',15)->
                end()->
        click('Filter',array(
            'charge_filters' => array(
                'kilometers' => array('to' => null))))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
            with('response')->
                begin()->
                    checkElement('div.sf_admin_list tbody tr',16)->
                end()
        
        
        
        ;

function getFormData($browser, $fields = array()) {


    $formFields = array(
        'category_id' =>$browser->getIdForCategory('tax'),
        'vehicle_id' => $browser->getVehicleId('vw-touran-1-4-tsi'),
        'kilometers' => 100,
        'amount' => 22,
        'comment' => '',
        'quantity' => null,
        'date' => array(
            'day' => 3,
            'month' => 2,
            'year' => 2010)
    );

    return array('charge' => array_merge($formFields, $fields));
}

