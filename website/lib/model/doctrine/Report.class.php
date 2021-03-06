<?php

/**
 * Report
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    otokou
 * @subpackage model
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Report extends BaseReport {

    protected $chart_builder = null;

    public function preSave($event) {

        parent::preSave($event);

        $invoker = $event->getInvoker();

        $vehicles = $invoker->getVehicles();

        $invoker->setNumVehicles(count($vehicles));
    }

    public function isCustom() {

        return $this->getNumVehicles() > 1 ? true : false;
    }

    public function getHash() {
        return sha1($this->getSlug());
    }

    public function getPdfFileName() {

        return $this->getHash() . '.pdf';
    }

    public function getPdfWebPath() {
        return sfConfig::get('app_report_dir_name');
    }

    public function getPdfFileWebPath() {
        return $this->getPdfWebPath() . '/' . $this->getPdfFileName();
    }

    public function getPdfSystemPath() {
        return sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $this->getPdfWebPath());
    }

    public function getPdfFileFullPath() {
        return $this->getPdfSystemPath() . DIRECTORY_SEPARATOR . $this->getPdfFileName();
    }
    
    public function generatePdf(sfContext $context, $chartBuilder, $file) {
        
        if ($this->hasCharges()) {
            
            $this->doGeneratePdf($context, $chartBuilder, $file);
            
            return true;
        } else {
            return false;
        }
        
    }



    protected function doGeneratePdf(sfContext $context, $chartBuilder, $file) {

        // definition of chart builder class
        $this->setChartBuilder($chartBuilder);

        // creation of a context
        $context->getConfiguration()->loadHelpers('Partial');
        $context->getRequest()->setRequestFormat('html');


        $config = sfTCPDFPluginConfigHandler::loadConfig();
        sfTCPDFPluginConfigHandler::includeLangFile($context->getUser()->getCulture());

        $doc_title = $this->getName() . " Report";
        $doc_subject = $this->getName() . " Report generated by Otokou";
        $doc_keywords = "report, otokou";

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
        $html = get_partial('report/general_info', array('report' => $this));
        $pdf->writeHTML($html, true, false, true, false, '');


        // vehicles overall performances
        $html = '<h1>Vehicles performances</h1>' .
                '<p>The values presented below are calculated overall the entire life period of the vehicle(s).</p>' .
                get_partial('report/vehicles_performances', array('vehicles' => $this->getVehicles()));
        $pdf->writeHTML($html, true, false, true, false, '');


        // charts
        $pdf->AddPage();
        $html = '<h1>Costs</h1>';
        $pdf->writeHTML($html, true, false, true, false, '');

        $counter = 0;
        $options = array();
        $attributes = array(
            'absolute' => true,
        );
        $charts = $this->defineCharts($options, $attributes);
        $nc = count($charts);
        foreach ($charts as $c) {

            $counter++;

            //$html = get_partial('report/chart', array('chart' => $c));
            //$pdf->writeHTML($html, true, false, true, false, '');

            $html = '<h2>'. $c['title'].'</h2><p>'.$c['comment'].'</p>';
            $pdf->writeHTML($html);
            
            $c['chart']->generate();
            $pdf->Image($c['chart']->getChartFileSystemPath());
            
            if ($counter < $nc) {
                $pdf->AddPage();
            }
        }


        // Close and output PDF document
        $pdf->Output($file, 'F');
    }

    public function defineCharts($options = array(), $attributes = array()) {

        $charts = array();


        $nv = count($this->getVehicles()->getPrimaryKeys());

        $rt = $nv > 1 ? 'date' : 'distance';


        $data = array(
            'range_type' => $rt,
            'chart_name' => 'cost_per_km',
        );
        $charts['cost_per_km'] = array(
            'title' => 'Cost per kilometer',
            'chart' => $this->defineChart($data, $options, $attributes),
            'comment' => 'The cost per kilometer is calculated by considering the charges registered over the entire life of the vehicle(s).',
        );


        $data = array(
            'range_type' => 'date',
            'chart_name' => 'cost_per_year',
        );
        $charts['cost_annual'] = array(
            'title' => 'Annual cost',
            'chart' => $this->defineChart($data, $options, $attributes),
            'comment' => 'The annual cost is calculated by considering the charges registered during the range (date and/or distance) specified for this report.',
        );


        $data = array(
            'range_type' => 'date',
            'chart_name' => 'cost_pie',
        );
        $charts['cost_allocation'] = array(
            'title' => 'Costs allocation',
            'chart' => $this->defineChart($data, $options, $attributes),
            'comment' => 'The cost allocation is calculated by considering the charges registered during the range (date and/or distance) specified for this report.',
        );


        $data = array(
            'chart_name' => 'trip_annual',
        );
        $charts['travel_annual'] = array(
            'title' => 'Annual travel',
            'chart' => $this->defineChart($data, $options, $attributes),
            'comment' => 'The annual travel is calculated by considering the charges registered during the range (date and/or distance) specified for this report.',
        );


        $data = array(
            'chart_name' => 'trip_monthly',
        );
        $charts['travel_monthly'] = array(
            'title' => 'Monthly travel',
            'chart' => $this->defineChart($data, $options, $attributes),
            'comment' => 'The monthly travel is calculated by considering the charges registered during the range (date and/or distance) specified for this report.',
        );


        $data = array(
            'range_type' => $rt,
            'chart_name' => 'consumption_per_distance',
        );
        $charts['consumption_fuel'] = array(
            'title' => 'Fuel consumption',
            'chart' => $this->defineChart($data, $options, $attributes),
            'comment' => 'The fuel consumption is calculated by considering the charges registered over the entire life of the vehicle(s).',
        );

        return $charts;
    }

    public function defineChart($params, $options = array(), $attributes = array()) {

        if (!$this->chart_builder) {
            throw new sfException('No Chart Builder class set. Please, use $this->setChartBuilder($cb) to define the class name');
        }

        $params = array_merge(
                array(
            'vehicles_list' => $this->getVehicles()->getPrimaryKeys(),
                ), $this->getGBData($params)
        );

        $params = $this->setRange($params);


        return new $this->chart_builder($params, $options, $attributes);
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

    protected function setRange($params) {

        if ($this->getDateFrom()) {
            $params['date_from'] = $this->getDateFrom();
        }

        if ($this->getDateTo()) {
            $params['date_to'] = $this->getDateTo();
        }

        if ($this->getKilometersFrom()) {
            $params['kilometers_from'] = $this->getKilometersFrom();
        }

        if ($this->getKilometersTo()) {
            $params['kilometers_to'] = $this->getKilometersTo();
        }

        return $params;
    }

    public function setChartBuilder($chartBuilder) {
        $this->chart_builder = $chartBuilder;
    }

    public function getChartBuilder() {

        return $this->chart_builder;
    }
    
    
    public function countCharges() {
        $q = Doctrine_Core::getTable('Charge')
                ->getAllByUserAndVehiclesQuery($this->getUserId(),$this->getVehicles()->getPrimaryKeys());
        
        
        $values = $this->objectRangesToArray();
        
        $q = Doctrine_Core::getTable('Charge')->addRangeQuery($q,$values);
        
        return $q->count();
        
    }
    
    
    public function objectRangesToArray() {
        
        $values = array();
        if (null !== $this->getDateFrom()) {
            $values['date_from'] = $this->getDateFrom();
        }
        
        if (null !== $this->getDateTo()) {
            $values['date_to'] = $this->getDateTo();
        }
        
        if (null !== $this->getKilometersFrom()) {
            $values['kilometers_from'] = $this->getKilometersFrom();
        }
        
        if (null !== $this->getKilometersTo()) {
            $values['kilometers_to'] = $this->getKilometersTo();
        }
        
        return $values;
        
        
    }
    
    
    public function hasCharges() {
        
        return $this->countCharges() > 0 ? true : false;                      
    }

}
