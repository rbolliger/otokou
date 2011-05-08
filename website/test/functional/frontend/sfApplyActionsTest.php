<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
include dirname(__FILE__) . '/../../../lib/test/otokouTestFunctional.class.php';

$browser = new otokouTestFunctional(new sfBrowser());

$browser->loadData()->logout();

$user = getUserData();

$browser->
        info('1 - Apply Action')->
        get('/apply')->
        click('Create My Account', array('sfApplyApply' => array(
            'username' => 'ruf',
            'email_address'     => 'sdtsdfsdsdsdsdcom',
            'email2'    => 'sdtsdf@sdsd.com',
        )))->
            with('form')->
                begin()->
                    hasErrors(4)->
                    isError('username','/invalid/')->
                    isError('password','/required/')->
                    isError('password2','/required/')->
                    isError('email_address','/invalid/')->
                end()->
        click('Create My Account', array('sfApplyApply' => $user))->
            with('form')->
                begin()->
                    hasErrors(false)->
                end()->
            with('mailer')->
                begin()->
                    checkHeader('Subject', '/Please verify your account on/')->
                end()->
       
        
        info('2 - Validate')->
        get(sprintf('/confirm/%s',getValidateKeyForUser($user['username'])))->
            with('response')->
                begin()->
                    checkElement('body','/Thank you for confirming your account/')->
                end()->
        click('Continue')->
            with('response')->
                begin()->
                    checkElement('body','/New Charge/')->
                end()->
        
        
        
        
        info('3 Reset password')->
                
        logout()->
        info('  3.1 Authenticated User')->
        login($user['username'],$user['password'])->
        get('/reset-request')->
            with('mailer')->
                begin()->
                    checkHeader('Subject', '/Please verify your password/')->
                end()->
            with('response')->
                begin()->
                    checkElement('body','/For security reasons, a confirmation message has been sent to/')->
                end()->
        click('Continue')->
            with('response')->
                begin()->
                    checkElement('body','/New Charge/')->
                end()->
        logout()->
        get(sprintf('/confirm/%s',getValidateKeyForUser($user['username'])))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
        with('request')->
            begin()->
                isParameter('module','sfApply')->
                isParameter('action','reset')->
            end()->
        click('Reset My Password',array('sfApplyReset' => array('password' => $user['password'],'password2' => $user['password'])))->
            with('response')->
                begin()->
                    checkElement('body','/Your password has been successfully reset/')->
                end()->
        click('Continue')->
            with('response')->
                begin()->
                    checkElement('body','/New Charge/')->
                end()->

        
        logout()->
        info('  3.2 Non-Authenticated User')->
        get('/reset-request')->
        click('Reset My Password',array('sfApplyResetRequest' => array('username_or_email' => $user['username'])))->
            with('mailer')->
                begin()->
                    checkHeader('Subject', '/Please verify your password/')->
                end()->
            with('response')->
                begin()->
                    checkElement('body','/For security reasons, a confirmation message has been sent to/')->
                end()->
        click('Continue')->
            with('response')->
                begin()->
                    checkElement('body','/Hello/')->
                end()->
        logout()->
        get(sprintf('/confirm/%s',getValidateKeyForUser($user['username'])))->
            with('response')->
                begin()->
                    isRedirected()->
                    followRedirect()->
                end()->
            with('request')->
                begin()->
                    isParameter('module','sfApply')->
                    isParameter('action','reset')->
                end()->
        click('Reset My Password',array('sfApplyReset' => array('password' => $user['password'],'password2' => $user['password'])))->
            with('response')->
                begin()->
                    checkElement('body','/Your password has been successfully reset/')->
                end()->
        click('Continue')->
            with('response')->
                begin()->
                    checkElement('body','/New Charge/')->
                end()
        
        ;


        
function getUserData() {
    return $user = array(
        'username' => 'dsgydx',
        'password' => '123456',
        'password2' => '123456',
        'email_address'     => 'sdtsdf@sdsd.com',
        'email2'    => 'sdtsdf@sdsd.com',
    );
}

function getUserFromDB($username) {
        
    return $user = Doctrine::getTable('sfGuardUser')->createQuery('u')->
            where('username = ?', $username)->
            fetchOne();
}


function getValidateKeyForUser($username) {
    
    return getUserFromDB($username)->getValidate();
}