<?php

/**
 * Description of otokouTestFunctional
 *
 * @author ruf
 */
class otokouTestFunctional extends sfTestFunctional {

    public function loadData() {
        Doctrine_Core::loadData(sfConfig::get('sf_test_dir') . '/fixtures');

        return $this;
    }

    public function getIdForCategory($category) {
        return $id = Doctrine_Core::getTable('Category')->findOneByName($category)->getId();
    }

    public function login() {

        $this->
                get('/login')->
                click('Signin', array(
                    'signin' => array(
                        'username' => 'ruf',
                        'password' => 'admin@1',
                        )));

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
        return $id = Doctrine_Core::getTable('Vehicle')->findOneBySlug($name)->getId();
    }

}

?>
