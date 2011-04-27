<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReportEmbeddedUserForm
 *
 * @author Raffaele Bolliger <raffaele.bolliger at gmail.com>
 */
class ReportEmbeddedUserForm extends ReportForm {

    public function configure() {

        parent::configure();

        unset($this['user_id']);

        $q = Doctrine_Core::getTable('Vehicle')
                        ->createQuery('v')
                        ->where('v.user_id = ?', $this->getUserId());

        $this->widgetSchema['vehicles_list']->setOption('query', $q);
        $this->validatorSchema['vehicles_list']->setOption('query', $q);
    }

    protected function getUserId() {

        return sfContext::getInstance()->getUser()->getGuarduser()->getId();
    }

    protected function doSave($con = null)
  {
    $this->getObject()->setUserId($this->getUserId());

    parent::doSave($con);
  }

}

