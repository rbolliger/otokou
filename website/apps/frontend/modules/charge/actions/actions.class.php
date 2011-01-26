<?php

require_once dirname(__FILE__) . '/../lib/chargeGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/chargeGeneratorHelper.class.php';

/**
 * charge actions.
 *
 * @package    otokou
 * @subpackage charge
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class chargeActions extends autoChargeActions {

    public function preExecute() {

        sfContext::getInstance()->getLogger()->info('{chargeActions} User logged is '.
                $this->getUser()->getGuardUser()->getUsername().
                ' ('.
                $this->getUser()->getGuardUser()->getId().
                ')'
                );
        
        //$this->checkOwnership();

        parent::preExecute();

        $this->context->getEventDispatcher()->connect("admin.build_query", array($this, 'addUserFilter'));
    }

    public function addUserFilter($event, $query) {

        $user_id = $this->getRequest()->getParameterHolder()->get('user_id') ?
                $this->getRequest()->getParameterHolder()->get('user_id') :
                $this->getUser()->getGuardUser()->getId();

        return $query->andWhere(sprintf('user_id = %d ', $user_id));
    }

    protected function checkOwnership() {

        $user_id = $this->getRequest()->getParameterHolder()->get('user_id') ?
                $this->getRequest()->getParameterHolder()->get('user_id') :
                $this->getUser()->getGuardUser()->getId();

        if ( $user_id == $this->getUser()->getGuardUser()->getId()) {
            $this->getUser()->addCredentials('owner');
        }
        
        if (!$this->getRequest()->getParameterHolder()->get('user_id')) {
            $this->getRequest()->getParameterHolder()->set('user_id',$user_id);
        }
    }
    
    public function postExecute() {
        $this->getUser()->removeCredential('owner');
    }

    
    public function getCredential() {
        
        $this->checkOwnership();
        
        return parent::getCredential();
    }

}
