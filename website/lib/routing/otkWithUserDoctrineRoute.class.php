<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of otkWithUserDoctrineRoute
 *
 * @author Raffaele Bolliger <raffaele.bolliger at gmail.com>
 */
class otkWithUserDoctrineRoute extends sfDoctrineRoute {

    public function generate($params, $context = array(), $absolute = false) {


        if ('object' !== $this->options['type'] || is_array($params)) {

            $params = array_merge(array('username' => $this->getUserId()),$params);
            
        }

        return parent::generate($params, $context, $absolute);
    }

    protected function doConvertObjectToArray($object) {


        $object = parent::doConvertObjectToArray($object);


        return array_merge($object, array('username' => $this->getUserId()));
    }

    protected function getUserId() {
        $user = sfContext::getInstance()->getUser();

        return $user->isAuthenticated() ? $user->getGuardUser()->getUsername() : 0;
    }

    protected function getRealVariables() {
        return array_merge(array('username'), parent::getRealVariables());
    }
    

}

