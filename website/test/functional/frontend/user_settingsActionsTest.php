<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new otokouTestFunctional(new sfBrowser());
$browser->loadData();
$browser->setTester('doctrine', 'sfTesterDoctrine');

$browser->
        info('1 - Account update')->
        
        info('  1.1 - Email address must be unique')->
          login('ruf','admin@1')->
          get('/ruf/settings/account')->
          click('Update',array('sf_guard_user' => array(
              'first_name'  => 'asdrgdfg',
              'last_name'   => 'sdgsdfgsdfg',
              'email_address' => 'uaser2@gmail.com'
          )))->
            with('form')->
                begin()->
                    hasErrors(1)->
                    isError('email_address','/invalid/')->
                end()->
        
        info('  1.2 - Email address is required')->
           click('Update',array('sf_guard_user' => array(
              'first_name'  => 'Ruf',
              'last_name'   => 'dfg',
              'email_address' => null
          )))-> 
                with('form')->
                begin()->
                    hasErrors(1)->
                    isError('email_address','/required/')->
                end()->
        
        info('  1.3 - A new email adress is accepted')->
            click('Update',array('sf_guard_user' => array(
              'first_name'  => 'Ruf',
              'last_name'   => 'dfg',
              'email_address' => 'asgsdfg.wetadf@asdg.com'
          )))-> 
                with('form')->
                    begin()->
                        hasErrors(false)->
                    end()

;
