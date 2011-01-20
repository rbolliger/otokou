<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ChargeEmbeddedUserForm
 *
 * @author Raffaele Bolliger <raffaele.bolliger at gmail.com>
 */
class ChargeEmbeddedUserForm extends ChargeForm {

    public function configure() {
        
        parent::configure();

        unset(
                $this['user_id']
        );
    }

    public function updateObject($values = null) {


        parent::updateObject($values);


        $user = sfContext::getInstance()->getUser();
        if (!$user->isAuthenticated()) {
            throw new sfException('The user must be authentified to save a charge form');
        }


        $this->getObject()->setUserId($user->getGuardUser()->getId());
    }

}
