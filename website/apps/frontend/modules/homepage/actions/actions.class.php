<?php

/**
 * homepage actions.
 *
 * @package    otokou
 * @subpackage homepage
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class homepageActions extends sfActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {
        if ($this->getUser()->isAuthenticated()) {
            $this->forward('charges', 'new');
        } else {
            $this->forward('homepage', 'welcome');
        }
    }

    public function executeWelcome(sfWebRequest $request) {
        
    }

}
