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

        $r = $this->getRoute()->getObject();

        $this->report = $r;

        $nv = count($r->getVehicles()->getPrimaryKeys());

        $rt = $nv > 1 ? 'date' : 'distance';

        $data = array(
            'range_type' => $rt,
            'chart_name' => 'cost_per_km',
        );
        $this->cost_per_km = $this->newChart($r,$data);

        $data = array(
            'range_type' => 'date',
            'chart_name' => 'cost_per_year',
        );
        $this->cost_annual = $this->newChart($r,$data);

        $data = array(
            'range_type' => 'date',
            'chart_name' => 'cost_pie',
        );
        $this->cost_allocation = $this->newChart($r,$data);
        $data = array(
            //'range_type' => 'date',
            'chart_name' => 'trip_annual',
        );
        $this->travel_annual = $this->newChart($r,$data);

        $data = array(
            //'range_type' => 'date',
            'chart_name' => 'trip_monthly',
        );
        $this->travel_monthly = $this->newChart($r,$data);

        $data = array(
            'range_type' => $rt,
            'chart_name' => 'consumption_per_distance',
        );
        $this->consumption_fuel = $this->newChart($r,$data);
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

    protected function getGBData($inputs = array()) {

        $data = array(
            'format' => 'png',
            'user_id' => $this->getUserId(),
            'vehicle_display' => 'single',
            'category_display' => 'stacked',
            'range_type' => 'distance',
        );

        return array_merge($data, $inputs);
    }

    protected function getUserId() {
        return $this->getUser()->getGuardUser()->getId();
    }

    protected function apply_range(Report $report, $params) {

        if ($report->getDateFrom()) {
            $params['date_from'] = $report->getDateFrom();
        }

        if ($report->getDateTo()) {
            $params['date_to'] = $report->getDateTo();
        }

        if ($report->getKilometersFrom()) {
            $params['kilometers_from'] = $report->getKilometersFrom();
        }

        if ($report->getKilometersTo()) {
            $params['kilometers_to'] = $report->getKilometersTo();
        }

        return $params;
    }

    protected function newChart($report, $params) {

        $params = array_merge(
                array(
                    'vehicles_list' => $report->getVehicles()->getPrimaryKeys(),
                    ),
                $this->getGBData($params)
                );

        $params = $this->apply_range($report, $params);

        return new ChartBuilderPChart($params);

    }

}
