<?php

/**
 * api actions.
 *
 * @package    otokou
 * @subpackage api
 * @author     Dave Bergomi
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class apiActions extends sfActions
{  
 /**
  * Executes index action
  *
  *
  * @param sfRequest $request A request object
  */
  public function executeGetUser(sfWebRequest $request)
  {
	$this->api = new ApiRR($this->getRequestParameter('request'),ApiRR::GET_USER_REQUEST);
	$this->api->treatRequest();
  }
  
    
 /**
  * Executes index action
  *
  *
  * @param sfRequest $request A request object
  */
  public function executeGetVehicles(sfWebRequest $request)
  {
	$this->api = new ApiRR($this->getRequestParameter('request'),ApiRR::GET_VEHICLES_REQUEST);
	$this->api->treatRequest();
  }
  
    
 /**
  * Executes index action
  *
  *
  * @param sfRequest $request A request object
  */
  public function executeSetCharge(sfWebRequest $request)
  {
	$this->api = new ApiRR($this->getRequestParameter('request'),ApiRR::SET_CHARGE_REQUEST);
	$this->api->treatRequest();
  }
  
 /**
  * Executes index action
  *
  *
  * @param sfRequest $request A request object
  */
  public function executeNotSecureError(sfWebRequest $request)
  {
	
  }
  
 /**
  * Executes index action
  *
  *
  * @param sfRequest $request A request object
  */
  public function executeNotPostError(sfWebRequest $request)
  {
	
  }

}
