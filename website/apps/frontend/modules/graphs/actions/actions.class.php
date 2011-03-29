<?php

/**
 * graphs actions.
 *
 * @package    otokou
 * @subpackage graphs
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class graphsActions extends sfActions {

    public function preExecute() {
        parent::preExecute();

        $this->filters = new GraphWithUserFormFilter($this->getFilters());

    }

    public function postExecute() {
        parent::postExecute();

        $this->data = $this->getData();

    }


    public function executeIndex(sfWebRequest $request) {

        $this->setPreviousTemplate('index');
        $this->setPreviousAction('index');

        $options = array();
        $this->gb = new GraphBuilderPChart($this->getGBData(),$options);

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

        $this->setFilterField('graph_name', 'cost_per_km');
        

        $options = array(
            'title' => 'Relative Cost [CHF/km]',
        );
        $this->gb = new GraphBuilderPChart($this->getGBData(),$options);

    }

    public function executeCostPerYear(sfWebRequest $request) {

        $this->setPreviousTemplate('costPerKm');
        $this->setPreviousAction('costPerYear');


        $filters = $this->getFilters();

        $filters = $this->updateFilterFieldIfEmpty($filters, 'vehicle_display', 'single');
        $filters = $this->updateFilterFieldIfEmpty($filters, 'category_display', 'stacked');
        $filters = $this->updateFilterFieldIfEmpty($filters, 'range_type', 'date');

        $this->setFilters($filters);

        $this->setFilterField('graph_name', 'cost_per_year');


        $options = array(
            'title' => 'Total Cost [CHF/year]',
        );
        $this->gb = new GraphBuilderPChart($this->getGBData(),$options);


        $this->setTemplate('costPerKm');

    }

    public function executeCostPie(sfWebRequest $request) {

        $this->setPreviousTemplate('costPerKm');
        $this->setPreviousAction('costPie');
        $this->setTemplate('costPerKm');

        // updating filters
        $filters = $this->getFilters();

        $filters = $this->updateFilterFieldIfEmpty($filters, 'vehicle_display', 'single');

        $this->setFilters($filters);

        $this->setFilterField('category_display', 'single'); // forced
        $this->setFilterField('graph_name', 'cost_pie');

        $options = array(
            'title' => 'Cost allocation [CHF]',
        );
        $this->gb = new GraphBuilderPChart($this->getGBData(),$options);



    }

    public function executeTripAnnual(sfWebRequest $request) {

        $this->setPreviousTemplate('costPerKm');
        $this->setPreviousAction('tripAnnual');
        $this->setTemplate('costPerKm');


        $filters = $this->getFilters();

        $filters = $this->updateFilterFieldIfEmpty($filters, 'vehicle_display', 'single');

        $this->setFilters($filters);

        $this->setFilterField('range_type', 'date');
        $this->setFilterField('graph_name', 'trip_annual');
        $this->setFilterField('category_display', 'stacked');


        $options = array(
            'title' => 'Annual travel [km/year]',
        );
        $this->gb = new GraphBuilderPChart($this->getGBData(),$options);
    }

    public function executeTripMonthly(sfWebRequest $request) {

        $this->setPreviousTemplate('costPerKm');
        $this->setPreviousAction('tripMOnthly');
        $this->setTemplate('costPerKm');


        $filters = $this->getFilters();

        $filters = $this->updateFilterFieldIfEmpty($filters, 'vehicle_display', 'single');

        $this->setFilters($filters);

        $this->setFilterField('range_type', 'date');
        $this->setFilterField('graph_name', 'trip_monthly');
        $this->setFilterField('category_display', 'stacked');


        $options = array(
            'title' => 'Monthly travel [km/year]',
        );
        $this->gb = new GraphBuilderPChart($this->getGBData(),$options);
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


        $this->gb = array();
        $this->setTemplate($this->getPreviousTemplate());
    }

    protected function setFilters(array $filters) {
        return $this->getUser()->setAttribute('graphs.filters', $filters, 'graphs');
    }

    protected function setFilterField($field, $value) {

        $filters = $this->getFilters();

        $filters[$field] = $value;

        $this->setFilters($filters);

    }

    protected function getFilters() {
        return $this->getUser()->getAttribute('graphs.filters', $this->getFilterDefaults(), 'graphs');
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
        return $this->getUser()->getAttribute('graphs.prevTemplate', null, 'graphs');
    }

    protected function setPreviousTemplate($template) {
        return $this->getUser()->setAttribute('graphs.prevTemplate', $template, 'graphs');
    }

    protected function getPreviousAction() {
        return 'graphs/' . $this->getUser()->getAttribute('graphs.prevAction', null, 'graphs');
    }

    protected function setPreviousAction($action) {
        return $this->getUser()->setAttribute('graphs.prevAction', $action, 'graphs');
    }

    protected function getData() {

        return $this->getFilters();
    }

    protected function updateFilterFieldIfEmpty($filters, $field, $value) {

        if (!isset($filters[$field])) {
            $filters[$field] = $value;
        }

        return $filters;
    }

    protected function getGBData() {

        $data = array(
            'format'            => 'png',
            'user_id'           => $this->getUser()->getGuardUser()->getId(),
            'vehicles_list'     => $this->getFilterValue('vehicles_list'),
            'vehicle_display'   => $this->getFilterValue('vehicle_display'),
            'categories_list'   => $this->getFilterValue('categories_list'),
            'category_display'  => $this->getFilterValue('category_display'),
            'range_type'        => $this->getFilterValue('range_type'),
            'date_from'         => $this->getFilterValue('from',null,$this->getFilterValue('date_range')),
            'date_to'           => $this->getFilterValue('to',null,$this->getFilterValue('date_range')),
            'kilometers_from'   => $this->getFilterValue('from',null,$this->getFilterValue('kilometers_range')),
            'kilometers_to'     => $this->getFilterValue('to',null,$this->getFilterValue('kilometers_range')),
            'graph_name'        => $this->getFilterValue('graph_name'),
        );


        return $data;
    }

    public function getFilterValue($field,$default = null,$filters = null) {

        $filters = (($filters === null) ? $this->getFilters() : $filters);

        return isset($filters[$field]) ? $filters[$field] : $default;

    }

}
