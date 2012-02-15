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
  * test version
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
	$this->api = new testApiRR($this->getRequestParameter('request'));
	$this->api->treatRequest();
  }
  
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
	$this->api = new ApiRR($this->getRequestParameter('request'),ApiRR::GET_VEHICLE_REQUEST);
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
  
}
