<?php

/**
 * graphs actions.
 *
 * @package    otokou
 * @subpackage graphs
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class graphsActions extends sfActions
{
        
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('default', 'module');
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
  
}
