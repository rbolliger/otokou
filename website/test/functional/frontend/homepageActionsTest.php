<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
include dirname(__FILE__) . '/../../../lib/test/otokouTestFunctional.class.php';

$browser = new otokouTestFunctional(new sfBrowser());

$browser->
        loadData()->
        info('1 - Welcome page')->
        
        info('  1.1 - Non-authenthicated User: the signin form is displayed')->
        logout()->
        get('/welcome')->
        with('response')->
        begin()->
        checkElement('input #signin_username',true)->
        end()->
        
        info('  1.2 - Authenthicated User: the signin form is NOT displayed')->
        login('ruf','admin@1')->
        get('/welcome')->
        with('response')->
        begin()->
        checkElement('input #signin_username',false)->
        end();
