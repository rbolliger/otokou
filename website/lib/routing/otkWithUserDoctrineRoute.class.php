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


            $params = array_merge(array('username' => $this->getUserId()), $params);

            //sfContext::getInstance()->getLogger()->info(print_r($params,true));
        }

        return parent::generate($params, $context, $absolute);
    }

    protected function doConvertObjectToArray($object) {


        $params = parent::doConvertObjectToArray($object);

        $params = array_merge(array('username' => $this->getUserId()), $params);
        return $params;
    }

    protected function getUserId() {
        $user = sfContext::getInstance()->getUser();

        return $user->isAuthenticated() ? $user->getGuardUser()->getUsername() : 0;
    }

    /*
      protected function getRealVariables() {
      return array_merge(array('username'), parent::getRealVariables());
      }
     * 
     */

    public function matchesParameters($params, $context = array()) {
        
        if ('object' !== $this->options['type'] || is_array($params)) {


            $params = array_merge(array('username' => $this->getUserId()), $params);

        }

        return parent::matchesParameters($params, $context);
    }

}

