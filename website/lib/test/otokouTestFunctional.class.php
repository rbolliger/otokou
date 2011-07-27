<?php

/**
 * Description of otokouTestFunctional
 *
 * @author ruf
 */
class otokouTestFunctional extends sfTestFunctional {

    public function __construct(sfBrowserBase $browser, lime_test $lime = null, $testers = array()) {
        parent::__construct($browser, $lime, $testers);

        $this->utility = new otkTestUtility();
        
    }
    
  
    public function loadData() {

        Doctrine::loadData(sfConfig::get('sf_test_dir') . '/fixtures');

        return $this;
    }

    public function getIdForCategory($category, $throw = true) {

        return $this->utility->getIdForCategory($category, $throw);
    }

    public function login($username = 'ruf', $password = 'admin@1') {

        $this->
                get('/login')->
                click('Signin', array(
                    'signin' => array(
                        'username' => $username,
                        'password' => $password,
                        )));

        $this->
                with('response')->
                begin()->
                isRedirected()->
                followRedirect()->
                end()
        ;


        return $this;
    }

    public function logout() {
        $this->
                get('/logout');

        $this->
                with('response')->
                begin()->
                isRedirected()->
                followRedirect()->
                end()->
                with('request')->
                begin()->
                isParameter('module', 'homepage')->
                isParameter('action', 'index')->
                end()
        ;


        return $this;
    }

    public function getUserId($username, $throw = true) {
        
        return $this->utility->getUserId($username,$throw);
    }

    public function getVehicleId($name, $throw = true) {

        return $this->utility->getVehicleId($name, $throw);
    }

    public function getOneChargeByParams($params = array()) {

        return $this->utility->getOneChargeByParams($params);
    }


    public function rmDirTree($directory) {

        return $this->utility->rmDirTree($directory);
    }
    

}

