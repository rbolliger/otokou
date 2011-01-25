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

            $params = array_merge(array('user_id' => $this->getUserId()),$params);
            
        }

        return parent::generate($params, $context, $absolute);
    }

    protected function doConvertObjectToArray($object) {


        $object = parent::doConvertObjectToArray($object);


        return array_merge($object, array('user_id' => $this->getUserId()));
    }

    protected function getUserId() {
        $user = sfContext::getInstance()->getUser();

        return $user->isAuthenticated() ? $user->getGuardUser()->getId() : 0;
    }

    protected function getRealVariables() {
        return array_merge(array('user_id'), parent::getRealVariables());
    }

}

