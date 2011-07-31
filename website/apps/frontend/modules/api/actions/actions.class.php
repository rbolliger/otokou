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
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
	$this->api = new apiRR($this->getRequestParameter('request'));	
	$this->api->treatRequest();
  }
}
