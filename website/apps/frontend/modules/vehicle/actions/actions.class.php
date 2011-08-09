<?php

require_once dirname(__FILE__) . '/../lib/vehicleGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/vehicleGeneratorHelper.class.php';

/**
 * vehicle actions.
 *
 * @package    otokou
 * @subpackage vehicle
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class vehicleActions extends autoVehicleActions {

    public function preExecute() {

        $this->dispatcher->connect('admin.pre_execute', array($this, 'addUserToConfig'));

        parent::preExecute();

        $this->dispatcher->connect('admin.build_query', array($this, 'addUserFilter'));
    }
    
    public function executeArchive(sfWebRequest $request) {

        $vehicle = $this->getRoute()->getObject();
        $vehicle->toggleArchive();

        $this->getUser()->setFlash('notice', sprintf('The selected vehicle has been successfully %s.',
                $vehicle->getIsArchived() ? 'archived' : 'unarchived'));

        $this->redirect('vehicle');
    }

    public function addUserFilter($event, $query) {

        return $query->andWhere(sprintf('user_id = %d ', $this->getUserIdFromRouteOrSession()));
    }

    public function addUserToConfig(sfEvent $event) {
        $this->configuration->setUserId($this->getUserIdFromRouteOrSession());
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

    public function getCredential() {

        $this->checkOwnership();

        return parent::getCredential();
    }

    

    protected function getUsernameFromRouteOrSession() {

        return $this->getRequest()->getParameterHolder()->get('username') ?
                $this->getRequest()->getParameterHolder()->get('username') :
                $this->getUser()->getGuardUser()->getUsername();
    }

    protected function getUserIdFromRouteOrSession() {

        $username = $this->getUsernameFromRouteOrSession();

        if ($username == $this->getUser()->getGuardUser()->getUsername()) {
            $user = $this->getUser()->getGuardUser();
        } else {
            $user = Doctrine_Core::getTable('sfGuardUser')->findOneByUsername($username);
        }

        $this->forward404Unless($user);

        return $user->getId();
    }
    
     public function executeNew(sfWebRequest $request) {
        $charge = new Vehicle();
        $charge->setUserId($this->getUserIdFromRouteOrSession());
        
        $this->form = $this->configuration->getForm($charge);
        $this->vehicle = $charge;
    }

    public function executeCreate(sfWebRequest $request) {
        
        $charge = new Vehicle();
        $charge->setUserId($this->getUserIdFromRouteOrSession());
        
        $this->form = $this->configuration->getForm($charge);
        $this->vehicle = $charge;


        $this->processForm($request, $this->form);

        $this->setTemplate('new');
    }

}
