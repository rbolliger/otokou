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

        $this->filters = new ChargeForGraphsFilter($this->getFilters());
    }

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {

        $this->setPreviousTemplate('index');

        $this->data = $this->getFilters();
        
    }

    public function executeFilter(sfWebRequest $request) {

        if ($request->hasParameter('_reset')) {
            $this->setFilters(array());

            $this->redirect($request->getReferer());
        }


        $this->filters->bind($request->getParameter($this->filters->getName()));

        if ($this->filters->isValid()) {

            $this->setFilters($this->filters->getValues());

            $this->redirect($request->getReferer());
        }


        $this->data = $this->getFilters();
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
        return $this->getUser()->getAttribute('graphs.prevTemplate', 'index', 'graphs');
    }

    protected function setPreviousTemplate($template) {
        return $this->getUser()->setAttribute('graphs.prevTemplate', $template, 'graphs');
    }

}
