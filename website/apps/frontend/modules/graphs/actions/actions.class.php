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

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {

        $this->setPreviousTemplate('index');
        $this->setPreviousAction('index');


        $this->data = $this->getData();
        
        $filters = $this->getFilters();
        
        $filters = $this->updateFieldIfEmpty($filters, 'vehicle_display', 'single');
        $filters = $this->updateFieldIfEmpty($filters, 'category_display', 'stacked');
        
        $this->query_results = $this->filters->buildQuery($filters)->execute();
            


        
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


        $this->data = $this->getData();
        $this->query_results = $this->filters->buildQuery($this->getFilters())->execute();
        $this->setTemplate($this->getPreviousTemplate());
    }

    protected function setFilters(array $filters) {
        return $this->getUser()->setAttribute('graphs.filters', $filters, 'graphs');
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
        return 'graphs/'.$this->getUser()->getAttribute('graphs.prevAction', null, 'graphs');
    }

    protected function setPreviousAction($action) {
        return $this->getUser()->setAttribute('graphs.prevAction', $action, 'graphs');
    }
    
    protected function getData() {
        
        return $this->getFilters();
        
    }
    
    protected function updateFieldIfEmpty($filters,$field,$value) {
        
        if (!in_array($field, array_keys($filters))) {
            $filters[$field] = $value;
        }

        return $filters;
        
    }

}
