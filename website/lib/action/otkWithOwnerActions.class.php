<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of otkWithOwnerActions
 *
 * @author Raffaele Bolliger <raffaele.bolliger at gmail.com>
 */
class otkWithOwnerActions extends sfActions {

    public function getCredential() {

        $this->checkOwnership();

        return parent::getCredential();
    }

    protected function checkOwnership() {

        $username = $this->getUsernameFromRouteOrSession();

        if ($username == $this->getUser()->getGuardUser()->getUsername()) {
            $this->getUser()->addCredentials('owner');
        } else {
            $this->getUser()->removeCredential('owner');
        }

        if (!$this->getRequest()->getParameterHolder()->get('username')) {
            $this->getRequest()->getParameterHolder()->set('username', $username);
        }
    }

    protected function getUsernameFromRouteOrSession() {

        return $this->getRequest()->getParameterHolder()->get('username') ?
                $this->getRequest()->getParameterHolder()->get('username') :
                $this->getUser()->getGuardUser()->getUsername();
    }

}

