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

        parent::preExecute();

        $this->context->getEventDispatcher()->connect("admin.build_query", array($this, 'addUserFilter'));
    }

    public function addUserFilter($event, $query) {

        $username = $this->getUserFromRouteOrSession();
        
        if ($username == $this->getUser()->getGuardUser()->getUsername()) {
                $id = $this->getUser()->getGuardUser()->getId();
        } else {
            $id = Doctrine_Core::getTable('sfGuardUser')->findOneByUsername($username)->getId();
        }

        return $query->andWhere(sprintf('user_id = %d ', $id));
    }

    protected function checkOwnership() {

        $username = $this->getUserFromRouteOrSession();

        if ( $username == $this->getUser()->getGuardUser()->getUsername()) {
            $this->getUser()->addCredentials('owner');
        } else {
            $this->getUser()->removeCredential('owner');
        }
        
        if (!$this->getRequest()->getParameterHolder()->get('username')) {
            $this->getRequest()->getParameterHolder()->set('username',$username);
        }
    }
    
    /*
    public function postExecute() {
        $this->getUser()->removeCredential('owner');
    }
     * 
     */

    
    public function getCredential() {
        
        $this->checkOwnership();
        
        return parent::getCredential();
    }
    
    protected function getUserFromRouteOrSession() {
        
        return  $this->getRequest()->getParameterHolder()->get('username') ?
                $this->getRequest()->getParameterHolder()->get('username') :
                $this->getUser()->getGuardUser()->getUsername();
    }
}
