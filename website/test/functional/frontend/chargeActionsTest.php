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
                with('doctrine')->
                    begin()->
                        check('Charge', array(
                          'id'     => $id = $browser->getOneChargeByParams(array('user_id' => $browser->getUserId('user2')))->getId(),
                        ),true)->
                    end()->
            call('/user2/charge/'.$id ,'delete', array('_with_csrf' => true))->
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
                    checkElement('.sf_admin_filter_field_vehicle_id ul li',1)->
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
                          'user_id'        => $browser->getUserId('ruf'),
                        ),16)->
                end()->
        
        info('  4.3 - The user can select multiple categories')->
        call('/ruf/charge/filter/action?_reset','post',array('_with_csrf' => true))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
        click('Filter',array(
                'charge_filters' => array(
                'category_id' => array($browser->getIdForCategory('Insurance'),$browser->getIdForCategory('Tax')))))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
            with('response')->
                begin()->
                    checkElement('div.sf_admin_list tbody tr',8)->
                end()->
            with('doctrine')->
                begin()->
                    check('Charge',
                            Doctrine_Core::getTable('Charge')->createQuery('a')
                                ->orWhere('a.category_id = ?',$browser->getIdForCategory('Insurance'))
                                ->orWhere('a.category_id = ?',$browser->getIdForCategory('Tax'))
                                ->andWhere('a.user_id = ?',$browser->getUserId('ruf'))
                            ,8)->
                end()->
        click('Filter',array(
                'charge_filters' => array(
                'category_id' => array($browser->getIdForCategory('Insurance'),$browser->getIdForCategory('Fuel')))))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
            with('response')->
                begin()->
                    checkElement('div.sf_admin_list tbody tr',10)->
                end()->
            with('doctrine')->
                begin()->
                    check('Charge',
                            Doctrine_Core::getTable('Charge')->createQuery('a')
                                ->orWhere('a.category_id = ?',$browser->getIdForCategory('Insurance'))
                                ->orWhere('a.category_id = ?',$browser->getIdForCategory('Fuel'))
                                ->andWhere('a.user_id = ?',$browser->getUserId('ruf'))
                            ,10)->
                end()->
        
        
        info('  4.4 - Kilometers (and Amount and quantity): the range filter works')->
        call('/ruf/charge/filter/action?_reset','post',array('_with_csrf' => true))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
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
                end()->
       
info('5 - Pagination')->
        logout()->
        login('user3','user3')->
        
        info('  5.1 - The number of displayed elements to show in list can be changed')->
        get('/user3/charge')->        
            with('response')->
                begin()->
                    checkElement('div.max_per_page',true)->
                    checkElement('div.sf_admin_pagination',true)->
                end()->
        
        info('  5.2 - By default, charges list show 20 elements')->
            with('response')->
                begin()->
                    checkElement('div.sf_admin_list tbody tr',20)->
                end()->
        
        info('  5.3 - If the User defines a different default value in his settings, the number of elements matches his choice')->
        logout()->
        login('user4','user4')->
        get('/user4/charge')-> 
            with('response')->
                    begin()->
                        checkElement('div.sf_admin_list tbody tr',3)->
                    end()->
            with('doctrine')->
                    begin()->
                        check('sfGuardUser', array(
                              'username'     => 'user4',
                              'list_max_per_page' => 3,
                            ),true)->
                    end()->
        
        info('  5.4 - The user can navigate through multiple pages')->
        click('a[href*="page=2"]')->
        with('response')->
                    begin()->
                        checkElement('div.sf_admin_list tbody tr',3)->
                    end()->
        
        info('  5.5 - Changing the number of elements returns to page 1 and displays the requested number of elements')->
        post('/user4/charge/maxPerPage/action', array('max_per_page' => 1000))->
            with('request')->
                begin()->
                    isParameter('action','maxPerPage')->
                    isParameter('max_per_page',1000)->
                end()->
            with('response')->
                    begin()->
                        isRedirected()->
                        followRedirect()->
                    end()->
            with('response')->
                begin()->
                    checkElement('div.sf_admin_list tbody tr',81)->
                    checkElement('select#max_per_page option[selected="selected"][value=1000]',1)->
                    checkElement('.sf_admin_pagination a[href*="page=2"]',false)->
                end()->
        post('/user4/charge/maxPerPage/action', array('max_per_page' => 50))->
            with('request')->
                begin()->
                    isParameter('action','maxPerPage')->
                    isParameter('max_per_page',50)->
                end()->
            with('response')->
                    begin()->
                        isRedirected()->
                        followRedirect()->
                    end()->
            with('response')->
                begin()->
                    checkElement('div.sf_admin_list tbody tr',50)->
                    checkElement('select#max_per_page option[selected="selected"][value=50]',1)->
                    checkElement('.sf_admin_pagination a[href*="page=2"]',true)->
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

