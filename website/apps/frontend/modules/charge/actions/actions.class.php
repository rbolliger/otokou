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

        $this->dispatcher->connect('admin.pre_execute', array($this, 'addUserToConfig'));

        parent::preExecute();

        $this->dispatcher->connect('admin.build_query', array($this, 'addUserFilter'));
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

    public function executeMaxPerPage(sfRequest $request) {

        $form = new PaginationMaxPerPageForm($this->getUser(), $this->getMaxPerPageOptions(), false);
        
        $isValid = $form->process($request);
        
        if ($isValid) {
           
            $this->redirect('@charge?page=1');
            
            }
        
        $this->pager = $this->getPager();
        $this->sort = $this->getSort();

        $this->setTemplate('index');
        $this->pager->form = $form;

    }

    protected function getMaxPerPageOptions() {

        
        $def = $this->getUser()->getGuardUser()->getListMaxPerPage() ? 
                            $this->getUser()->getGuardUser()->getListMaxPerPage() : 
                            $this->configuration->getGeneratorMaxPerPage();

        
        $options = array(
            'max_per_page_name' => 'charge_list_max_per_page',
            'max_per_page_choices' => array(
                10,
                20,
                50,
                100,
                150,
                1000,
            ),
            'max_per_page_value' => $def,
        );

        return $options;
    }
    
   public function executeIndex(sfWebRequest $request) {
              
       parent::executeIndex($request); 

       $this->pager->form = new PaginationMaxPerPageForm($this->getUser(), $this->getMaxPerPageOptions(), false);
           
    }
    
    public function executeFilter(sfWebRequest $request) {
        
        parent::executeFilter($request);
        
        $this->pager->form = new PaginationMaxPerPageForm($this->getUser(), $this->getMaxPerPageOptions(), false);
    }

}
