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

        if ($r->getIsNew()) {
            $r->setIsNew(false);
            $r->save();
        }

        $this->report = $r;
        $this->charts = $this->getCharts($r);
    }

    public function executePdf(sfWebRequest $request) {

        $r = $this->getRoute()->getObject();

        if ($r->getIsNew()) {
            $r->setIsNew(false);
            $r->save();
        }

        $web_file = sfConfig::get('app_report_dir_name') . DIRECTORY_SEPARATOR . $r->getHash() . '.pdf';
        $file = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . $web_file;

        if (!file_exists($file) || sfConfig::get('app_report_force_generate')) {
            $this->generatePdf($r, $web_file);
        }

        // disbale the layout
        $this->setLayout(false);

        $response = $this->getResponse();

        // return the binary pdf dat directly in the response as if serving a static pdf file
        $response->setHttpHeader('Content-Disposition', 'attachment; filename="' . $web_file . '"');
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

    protected function generatePdf(Report $report, $filename) {

        $config = sfTCPDFPluginConfigHandler::loadConfig();
        sfTCPDFPluginConfigHandler::includeLangFile($this->getUser()->getCulture());

        $doc_title = $report->getName() . " Report";
        $doc_subject = $report->getName() . " Report generated by Otokou";
        $doc_keywords = "report, otokou";
//        $htmlcontent = $this->getController()->getPresentationFor('report', 'show');
        //create new PDF document (document units are set by default to millimeters)
        $pdf = new sfTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);
        $pdf->SetTitle($doc_title);
        $pdf->SetSubject($doc_subject);
        $pdf->SetKeywords($doc_keywords);


        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        //initialize document
        $pdf->AliasNbPages();


        // report informations
        $pdf->AddPage();
        $html = $this->getPartial('report/general_info', array('report' => $report));
        $pdf->writeHTML($html, true, false, true, false, '');


        // vehicles overall performances
        $html = '<h1>Vehicles performances</h1>' .
                '<p>The values presented below are calculated overall the entire life period of the vehicle(s).</p>' .
                $this->getPartial('report/vehicles_performances', array('vehicles' => $report->getVehicles()));
        $pdf->writeHTML($html, true, false, true, false, '');


        // charts
        $pdf->AddPage();
        $html = '<h1>Costs</h1>';
        $pdf->writeHTML($html, true, false, true, false, '');

        $counter = 0;
        $charts = $this->getCharts($report);
        $nc = count($charts);
        foreach ($charts as $c) {

            $counter++;

            $html = $this->getPartial('report/chart', array('chart' => $c));
            $pdf->writeHTML($html, true, false, true, false, '');

            if ($counter < $nc) {
                $pdf->AddPage();
            }
        }


        // Close and output PDF document
        $pdf->Output($filename, 'F');
    }

    protected function getCharts(Report $report) {

        $charts = array();

        $nv = count($report->getVehicles()->getPrimaryKeys());

        $rt = $nv > 1 ? 'date' : 'distance';


        $data = array(
            'range_type' => $rt,
            'chart_name' => 'cost_per_km',
        );
        $charts['cost_per_km'] = array(
            'title' => 'Cost per kilometer',
            'chart' => $this->newChart($report, $data),
            'comment' => 'The cost per kilometer is calculated by considering the charges registered over the entire life of the vehicle(s).',
        );


        $data = array(
            'range_type' => 'date',
            'chart_name' => 'cost_per_year',
        );
        $charts['cost_annual'] = array(
            'title' => 'Annual cost',
            'chart' => $this->newChart($report, $data),
            'comment' => 'The annual cost is calculated by considering the charges registered during the range (date and/or distance) specified for this report.',
        );


        $data = array(
            'range_type' => 'date',
            'chart_name' => 'cost_pie',
        );
        $charts['cost_allocation'] = array(
            'title' => 'Costs allocation',
            'chart' => $this->newChart($report, $data),
            'comment' => 'The cost allocation is calculated by considering the charges registered during the range (date and/or distance) specified for this report.',
        );


        $data = array(
            'chart_name' => 'trip_annual',
        );
        $charts['travel_annual'] = array(
            'title' => 'Annual travel',
            'chart' => $this->newChart($report, $data),
            'comment' => 'The annual travel is calculated by considering the charges registered during the range (date and/or distance) specified for this report.',
        );


        $data = array(
            'chart_name' => 'trip_monthly',
        );
        $charts['travel_monthly'] = array(
            'title' => 'Monthly travel',
            'chart' => $this->newChart($report, $data),
            'comment' => 'The monthly travel is calculated by considering the charges registered during the range (date and/or distance) specified for this report.',
        );


        $data = array(
            'range_type' => $rt,
            'chart_name' => 'consumption_per_distance',
        );
        $charts['consumption_fuel'] = array(
            'title' => 'Fuel consumption',
            'chart' => $this->newChart($report, $data),
            'comment' => 'The fuel consumption is calculated by considering the charges registered over the entire life of the vehicle(s).',
        );

        return $charts;
    }

}
