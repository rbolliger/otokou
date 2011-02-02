<?php

/**
 * Description of otokouTestFunctional
 *
 * @author ruf
 */
class otokouTestFunctional extends sfTestFunctional {

    public function loadData() {
        //new sfDatabaseManager(sfContext::getInstance()->getConfiguration());
        //Doctrine::createTablesFromModels(dirname(__FILE__).'/../model'); 
        Doctrine_Core::loadData(sfConfig::get('sf_test_dir') . '/fixtures');

        return $this;
    }

    public function getIdForCategory($category) {
        return $id = Doctrine_Core::getTable('Category')->findOneByName($category)->getId();
    }

    public function login($username = 'ruf',$password = 'admin@1') {

        $this->
                get('/login')->
                click('Signin', array(
                    'signin' => array(
                        'username' => $username,
                        'password' => $password,
                        )));

        $this->
                with('response')->
                isRedirected()->
                followRedirect()
        ;


        return $this;
    }
    
    public function logout() {
        $this->
                get('/logout');
        
        $this->
                with('response')->
                isRedirected()->
                followRedirect()
        ;


        return $this;
        
    }

    public function getUserId($username) {
        return $id = Doctrine_Core::getTable('sfGuardUser')->findOneByUsername($username)->getId();
    }

    public function getVehicleId($name) {
        
        $v = Doctrine_Core::getTable('Vehicle')->findOneBySlug($name);
        
        return $v ? $v->getId() : null;
    }
    
    public function getOneChargeByParams($params = array()) {
        
        if (!$params) {
            throw new sfException('At least one parameter must be specified');
        }
        
        $q = Doctrine_Core::getTable('Charge')->createQuery('c');
        
        foreach ($params as $key => $value) {
            $q->andWhere('c.'.$key.' = ?',$value);
        }
        
        return $q->fetchOne();
        
        
        
    }

}

?>
