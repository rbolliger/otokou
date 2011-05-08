<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
include dirname(__FILE__) . '/../../../lib/test/otokouTestFunctional.class.php';

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
                    end()->
                with('response')->
                    begin()->
                        isRedirected()->
                        followRedirect()->
                    end()->
                with('request')->
                    begin()->
                        isParameter('module','user_settings')->
                        isParameter('action','account')->
                    end()->
        
        
        
        info('2 - Otokou settings')->
        
        info('  2.1 - List max_per_page')->
        get('/ruf/settings/otokou')->   
            with('response')->
              begin()->
                isStatusCode(200)->
              end()->
        click('Update',array('sf_guard_user' =>
            array(
                'list_max_per_page' => -0.3
            )))->
            with('form')->
                begin()->
                    hasErrors(1)->
                    isError('list_max_per_page','invalid')->
                end()->
         click('Update',array('sf_guard_user' =>
            array(
                'list_max_per_page' => 25
            )))->
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
                    isParameter('module','user_settings')->
                    isParameter('action','otokou')->
                end()->
            with('doctrine')->
                begin()->
                    check('sfGuardUser', array(
                          'id'     => $browser->getUserId('ruf'),
                          'list_max_per_page' => 25
                        ),true)->
                end()->
        click('Update',array('sf_guard_user' =>
            array(
                'list_max_per_page' => null
            )))->
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
                    isParameter('module','user_settings')->
                    isParameter('action','otokou')->
                end()->
            with('doctrine')->
                begin()->
                    check('sfGuardUser', array(
                          'id'     => $browser->getUserId('ruf'),
                          'list_max_per_page' => 25
                        ),false)->
                    check('sfGuardUser', array(
                          'id'     => $browser->getUserId('ruf'),
                          'list_max_per_page' => null
                        ),true)->
                end()->
        
        info('3 - Vehicles settings')->
        
        click('Manage vehicles')->
            with('request')-> 
                begin()->
                    isParameter('module','vehicle')->
                    isParameter('action','index')->
                end()->
        click('Edit',array(), array('position' => 1))->
            with('request')->
                begin()->
                    isParameter('module','vehicle')->
                    isParameter('action','edit')->
                end()->
            with('response')->
                begin()->
                    checkElement('div.sf_admin_form_row',2)-> // only Name and isArchived must appear
                end()
        

;
