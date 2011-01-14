<?php

/**
 * charges actions.
 *
 * @package    otokou
 * @subpackage charges
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class chargesActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        $this->charges = $this->getRoute()->getObjects();
    }

    public function executeNew(sfWebRequest $request) {

        $this->form = new ChargeForm();
    }

    public function executeCreate(sfWebRequest $request) {
        $this->form = new ChargeForm();

        $this->processForm($request, $this->form);

        $this->setTemplate('new');
    }

    public function executeEdit(sfWebRequest $request) {
        $this->form = new ChargeForm($this->getRoute()->getObject());
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->form = new ChargeForm($this->getRoute()->getObject());

        $this->processForm($request, $this->form);

        $this->setTemplate('edit');
    }

    public function executeDelete(sfWebRequest $request) {
        $request->checkCSRFProtection();

        $this->getRoute()->getObject()->delete();

        $this->redirect('@charges');
    }

    protected function processForm(sfWebRequest $request, sfForm $form) {
        
        
        $userId = $this->getUser()->getGuardUser()->getId();
        
        $taintedValues = $request->getParameter($form->getName());
        $taintedValues['user_id'] = $userId;
        
        
        $form->bind($taintedValues, $request->getFiles($form->getName()));
        if ($form->isValid()) {
            
            

            //$form->updateObject(array('user_id' => $userId));

            $charge = $form->save();

            $this->redirect('@charges_edit?id=' . $charge->getId());
        }
    }

}
