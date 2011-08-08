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

        $this->vehicles = $this->getRoute()->getObjects();

        $this->custom = Doctrine_Core::getTable('Report')->findCustomReportsByUser(
                        array('username' => $request->getParameter('username'),
                            'max' => sfConfig::get('app_report_max_on_index'),
                ));
    }

    public function executeListVehicle(sfWebRequest $request) {

        $this->vehicle = $this->getRoute()->getObject();



        $this->pager = new sfDoctrinePager(
                        'Report',
                        sfConfig::get('app_report_max_on_list')
        );
        $this->pager->setQuery($this->vehicle->getOwnReportsQuery());
        $this->pager->setPage($request->getParameter('page', 1));
        $this->pager->init();
    }

    public function executeListCustom(sfWebRequest $request) {

        $this->custom = $this->getRoute()->getObjects();
    }

    public function executeNew(sfWebRequest $request) {
        
        $report = $this->newReport();

        //$this->form = new ReportEmbeddedUserForm();
        $this->form = new ReportForm($report);
    }

    public function executeCreate(sfWebRequest $request) {
//        $this->form = new ReportEmbeddedUserForm();
        
        $report = $this->newReport();
        $this->form = new ReportForm($report);

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

        $r = $this->getRoute()->getObject();

        if ($r->getIsNew()) {
            $r->setIsNew(false);
            $r->save();
        }

        $this->report = $r;
        
        
        $r->setChartBuilder('ChartBuilderPChart'); 
        $this->charts = $r->defineCharts();
    }

    public function executePdf(sfWebRequest $request) {

        $r = $this->getRoute()->getObject();

        if ($r->getIsNew()) {
            $r->setIsNew(false);
            $r->save();
        }

        $file = $r->getPdfFileFullPath(); 

        if (!file_exists($file) || sfConfig::get('app_report_force_generate')) {
            $status = $r->generatePdf($this->getContext(), 'ChartBuilderPChart', $file);
        }

        if (!$status) {

            return sfView::SUCCESS;
        }
        
        // disbale the layout
        $this->setLayout(false);

        $response = $this->getResponse();

        // return the binary pdf dat directly in the response as if serving a static pdf file
        $response->setHttpHeader('Content-Disposition', 'attachment; filename="' . $r->getSlug() . '"');
        $response->setContentType('application/pdf');
        $response->setContent(file_get_contents($file));

        return sfView::NONE;
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
    
    
    protected function newReport() {
        
        $report = new Report();
        $report->setUserId($this->getUser()->getGuardUser()->getId());
        
        return $report;
        
    }

}
