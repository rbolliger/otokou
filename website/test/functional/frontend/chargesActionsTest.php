<?php

include(dirname(__FILE__) . '/../../bootstrap/functional.php');

$browser = new otokouTestFunctional(new sfBrowser());
$browser->loadData()->login();




$fuelId = $browser->getIdForCategory('fuel');






$browser->
        info('0 - The homepage is redirected to charges/new')->
        get('/')->
        with('request')->
        begin()->
        isParameter('module', 'charges')->
        isParameter('action', 'new')->
        end();

$browser->info('1 - The charges form')->
        info('  1.1 - If "fuel" category is selected, a quantity must be specified')->
        get('/charges/new')->
        with('request')->
        begin()->
        isParameter('module', 'charges')->
        isParameter('action', 'new')->
        end()->
        click('Save', getFormData($browser, array('category_id' => $fuelId, 'quantity' => null)))->
        with('form')->
        begin()->
        hasErrors()->
        isError('quantity', '/quantity/')->
        end()->
        click('Save', getFormData($browser, array('category_id' => $fuelId, 'quantity' => 12)))->
        with('form')->
        begin()->
        hasErrors(false)->
        end()->
        
        info(' 1.2 - If any other category is selected, the quantity cannot be specified')->
        get('/charges/new')->
        click('Save', getFormData($browser, array('category_id' => $browser->getIdForCategory('Tax'),'quantity' => 12)))->
        with('form')->
        begin()->
        hasErrors()->
        isError('quantity', '/quantity/')->
        end()->
        click('Save', getFormData($browser, array('category_id' => $browser->getIdForCategory('Tax'), 'quantity' => null)))->
        with('form')->begin()->
        hasErrors(false)->
        end();


function getFormData($browser, $fields = array()) {
    
    
    $formFields = array(
        'vehicle_id' => $browser->getVehicleId('vw-touran-1-4-tsi'),
        'user_id' => $browser->getUserId('ruf'),
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