<?php

/**
 * Vehicle form.
 *
 * @package    otokou
 * @subpackage form
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class VehicleEmbeddedUserForm extends VehicleForm {

    public function configure() {

        parent::configure();

        unset(
                $this['user_id'],
                $this['charts_list'],
                $this['reports_list']
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
