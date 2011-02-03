<?php

/**
 * user_settings actions.
 *
 * @package    otokou
 * @subpackage user_settings
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class user_settingsActions extends sfActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeAccount(sfWebRequest $request) {


        $user = $this->getRoute()->getObject();
        $this->form = new sfGuardUserAccountForm($user);
 


        if ($request->isMethod('put')) {
            $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

            if ($this->form->isValid()) { 
                $this->form->save();
                
                $this->getUser()->setFlash('notice', 'The account details have been saved.');

                $this->redirect('@user_settings_account');
            }
        }
    }

}
