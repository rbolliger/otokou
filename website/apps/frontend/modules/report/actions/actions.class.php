<?php

/**
 * report actions.
 *
 * @package    otokou
 * @subpackage report
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class reportActions extends otkWithOwnerActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {

        $this->reports = $this->getRoute()->getObjects();
    }

    public function executeListVehicle(sfWebRequest $request) {

        $this->reports = $this->getRoute()->getObjects();
    }

    public function executeListCustom(sfWebRequest $request) {

        $this->reports = $this->getRoute()->getObjects();

        $this->setTemplate('listVehicle');
    }

    public function executeNew(sfWebRequest $request) {

        $this->form = new ReportEmbeddedUserForm();
    }

    public function executeCreate(sfWebRequest $request) {
        $this->form = new ReportEmbeddedUserForm();

        $this->processForm($request, $this->form);

        $this->setTemplate('new');
    }

    public function executeDelete(sfWebRequest $request) {
        $request->checkCSRFProtection();

        $object = $this->getRoute()->getObject();

        if ($object->isCustom()) {
            $redirect = '@reports_list_custom';
        } else {
            $vehicles = $object->getVehicles();
            $slug = $vehicles[0]->getSlug();
            $redirect = '@reports_list_vehicle?slug=' . $slug;
        }

        if ($object->delete()) {
            $this->getUser()->setFlash('notice', 'The report was deleted successfully.');
        }

        $this->redirect($redirect);
    }

    public function executeShow(sfWebRequest $request) {
        
    }

    public function checkCSRFProtection() {
        $form = new BaseForm();
        $form->bind($form->isCSRFProtected() ? array($form->getCSRFFieldName() => $this->getParameter($form->getCSRFFieldName())) : array());

        if (!$form->isValid()) {
            throw $form->getErrorSchema();
        }
    }

    protected function processForm(sfWebRequest $request, sfForm $form) {
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {
            try {
                $report = $form->save();
            } catch (Doctrine_Validator_Exception $e) {

                $errorStack = $form->getObject()->getErrorStack();

                $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ? 's' : null) . " with validation errors: ";
                foreach ($errorStack as $field => $errors) {
                    $message .= "$field (" . implode(", ", $errors) . "), ";
                }
                $message = trim($message, ', ');

                $this->getUser()->setFlash('error', $message);
                return sfView::SUCCESS;
            }

            $this->redirect('report/show?slug=' . $report->getSlug());
        } else {
            $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
        }
    }

}
