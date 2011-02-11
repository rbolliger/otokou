<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of otkWithUserRoute
 *
 * @author Raffaele Bolliger <raffaele.bolliger at gmail.com>
 */
class otkWithUserRoute extends sfRequestRoute {

    public function generate($params, $context = array(), $absolute = false) {


        $params = array_merge(array('username' => $this->getUserId()), $params);


        return parent::generate($params, $context, $absolute);
    }

    protected function getUserId() {
        $user = sfContext::getInstance()->getUser();

        return $user->isAuthenticated() ? $user->getGuardUser()->getUsername() : 0;
    }


    public function matchesParameters($params, $context = array()) {

        $params = array_merge(array('username' => $this->getUserId()), $params);
        
        return parent::matchesParameters($params, $context);
    }
    
    
    

}

