<?php

/**
 * charts actions.
 *
 * @package    otokou
 * @subpackage charts
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class chartsActions extends sfActions {

    public function preExecute() {
        parent::preExecute();

        $this->filters = new ChartWithUserFormFilter($this->getFilters());
    }

    public function postExecute() {
        parent::postExecute();

        $this->debug = $this->getDebugData();
    }

    public function executeIndex(sfWebRequest $request) {

        $this->setPreviousTemplate('index');
        $this->setPreviousAction('index');


        $this->vehicles = $this->getRequestedVehicles();
    }

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeCostPerKm(sfWebRequest $request) {

        $this->setPreviousTemplate('costPerKm');
        $this->setPreviousAction('costPerKm');


        $filters = $this->getFilters();
        $filters = $this->updateFilterFieldIfEmpty($filters, 'vehicle_display', 'single');
        $filters = $this->updateFilterFieldIfEmpty($filters, 'category_display', 'stacked');
        $filters = $this->updateFilterFieldIfEmpty($filters, 'range_type', 'distance');
        $this->setFilters($filters);
        $this->setFilterField('chart_name', 'cost_per_km');

        $this->gb = new ChartBuilderPChart($this->getGBData());
    }

    public function executeCostPerYear(sfWebRequest $request) {

        $this->setPreviousTemplate('costPerKm');
        $this->setPreviousAction('costPerYear');
        $this->setTemplate('costPerKm');

        $filters = $this->getFilters();
        $filters = $this->updateFilterFieldIfEmpty($filters, 'vehicle_display', 'single');
        $filters = $this->updateFilterFieldIfEmpty($filters, 'category_display', 'stacked');
        $filters = $this->updateFilterFieldIfEmpty($filters, 'range_type', 'date');
        $this->setFilters($filters);
        $this->setFilterField('chart_name', 'cost_per_year');

        $this->gb = new ChartBuilderPChart($this->getGBData());
    }

    public function executeCostPie(sfWebRequest $request) {

        $this->setPreviousTemplate('costPerKm');
        $this->setPreviousAction('costPie');
        $this->setTemplate('costPerKm');

        // updating filters
        $filters = $this->getFilters();
        $filters = $this->updateFilterFieldIfEmpty($filters, 'vehicle_display', 'single');
        $this->setFilters($filters);
        $this->setFilterField('chart_name', 'cost_pie');

        $this->gb = new ChartBuilderPChart($this->getGBData());
    }

    public function executeTripAnnual(sfWebRequest $request) {

        $this->setPreviousTemplate('costPerKm');
        $this->setPreviousAction('tripAnnual');
        $this->setTemplate('costPerKm');


        $filters = $this->getFilters();
        $filters = $this->updateFilterFieldIfEmpty($filters, 'vehicle_display', 'single');
        $this->setFilters($filters);
        $this->setFilterField('chart_name', 'trip_annual');

        $this->gb = new ChartBuilderPChart($this->getGBData());
    }

    public function executeTripMonthly(sfWebRequest $request) {

        $this->setPreviousTemplate('costPerKm');
        $this->setPreviousAction('tripMonthly');
        $this->setTemplate('costPerKm');

        $filters = $this->getFilters();
        $filters = $this->updateFilterFieldIfEmpty($filters, 'vehicle_display', 'single');
        $this->setFilters($filters);
        $this->setFilterField('chart_name', 'trip_monthly');

        $this->gb = new ChartBuilderPChart($this->getGBData());
    }

    public function executeConsumptionPerDistance(sfWebRequest $request) {

        $this->setPreviousTemplate('costPerKm');
        $this->setPreviousAction('consumptionPerDistance');
        $this->setTemplate('costPerKm');

        $filters = $this->getFilters();
        $filters = $this->updateFilterFieldIfEmpty($filters, 'vehicle_display', 'single');
        $filters = $this->updateFilterFieldIfEmpty($filters, 'range_type', 'distance');
        $this->setFilters($filters);
        $this->setFilterField('chart_name', 'consumption_per_distance');

        $this->gb = new ChartBuilderPChart($this->getGBData());
    }

    public function executeFilter(sfWebRequest $request) {

        if ($request->hasParameter('_reset')) {

            $this->setFilters(array());

            $this->redirect($this->getPreviousAction());
        }


        $this->filters->bind($request->getParameter($this->filters->getName()));

        if ($this->filters->isValid()) {

            $this->setFilters($this->filters->getValues());

            $this->redirect($this->getPreviousAction());
        }

        // In case of error, we redisplay the filter form with messages

        $this->gb = array();
        $templ = $this->getPreviousTemplate();
        $this->setTemplate($templ);

        if ('index' == $templ) {
            $this->vehicles = $this->getRequestedVehicles();
        }
    }

    protected function setFilters(array $filters) {
        return $this->getUser()->setAttribute('charts.filters', $filters, 'charts');
    }

    protected function setFilterField($field, $value) {

        $filters = $this->getFilters();

        $filters[$field] = $value;

        $this->setFilters($filters);
    }

    protected function getFilters() {
        return $this->getUser()->getAttribute('charts.filters', $this->getFilterDefaults(), 'charts');
    }

    protected function checkOwnership() {

        $username = $this->getUsernameFromRouteOrSession();

        if ($username == $this->getUser()->getGuardUser()->getUsername()) {
            $this->getUser()->addCredentials('owner');
        } else {
            $this->getUser()->removeCredential('owner');
        }

        if (!$this->getRequest()->getParameterHolder()->get('username')) {
            $this->getRequest()->getParameterHolder()->set('username', $username);
        }
    }

    public function getCredential() {

        $this->checkOwnership();

        return parent::getCredential();
    }

    protected function getUsernameFromRouteOrSession() {

        return $this->getRequest()->getParameterHolder()->get('username') ?
                $this->getRequest()->getParameterHolder()->get('username') :
                $this->getUser()->getGuardUser()->getUsername();
    }

    protected function getFilterDefaults() {

        return array();
    }

    protected function getPreviousTemplate() {
        return $this->getUser()->getAttribute('charts.prevTemplate', null, 'charts');
    }

    protected function setPreviousTemplate($template) {
        return $this->getUser()->setAttribute('charts.prevTemplate', $template, 'charts');
    }

    protected function getPreviousAction() {
        return 'charts/' . $this->getUser()->getAttribute('charts.prevAction', null, 'charts');
    }

    protected function setPreviousAction($action) {
        return $this->getUser()->setAttribute('charts.prevAction', $action, 'charts');
    }

    protected function getDebugData() {

        if (!in_array(sfConfig::get('sf_environment'), array('dev', 'test'))) {
            return array();
        }

        $debug = array(
            'filters' => $this->getFilters(),
            'gb' => isset($this->gb) ? $this->gb : array(),
        );

        return $debug;
    }

    protected function getRequestedVehicles() {

        $filter_vehicles = $this->getFilterValue('vehicles_list');
        $user_id = $this->getUserId();

        return $vehicles = Doctrine_Core::getTable('Vehicle')->findByUserIdAndVehicleId($user_id, $filter_vehicles);
    }

    protected function getUserId() {
        return $this->getUser()->getGuardUser()->getId();
    }

    protected function updateFilterFieldIfEmpty($filters, $field, $value) {

        if (!isset($filters[$field])) {
            $filters[$field] = $value;
        }

        return $filters;
    }

    protected function getGBData() {

        $data = array(
            'format' => 'png',
            'user_id' => $this->getUserId(),
            'vehicles_list' => $this->getFilterValue('vehicles_list'),
            'vehicle_display' => $this->getFilterValue('vehicle_display'),
            'categories_list' => $this->getFilterValue('categories_list'),
            'category_display' => $this->getFilterValue('category_display'),
            'range_type' => $this->getFilterValue('range_type'),
            'date_from' => $this->getFilterValue('from', null, $this->getFilterValue('date_range')),
            'date_to' => $this->getFilterValue('to', null, $this->getFilterValue('date_range')),
            'kilometers_from' => $this->getFilterValue('from', null, $this->getFilterValue('kilometers_range')),
            'kilometers_to' => $this->getFilterValue('to', null, $this->getFilterValue('kilometers_range')),
            'chart_name' => $this->getFilterValue('chart_name'),
        );


        return $data;
    }

    public function getFilterValue($field, $default = null, $filters = null) {

        $filters = (($filters === null) ? $this->getFilters() : $filters);

        return isset($filters[$field]) ? $filters[$field] : $default;
    }

}
