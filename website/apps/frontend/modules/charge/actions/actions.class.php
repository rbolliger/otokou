<?php

require_once dirname(__FILE__) . '/../lib/chargeGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/chargeGeneratorHelper.class.php';

/**
 * charge actions.
 *
 * @package    otokou
 * @subpackage charge
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class chargeActions extends autoChargeActions {

    public function preExecute() {

        $this->dispatcher->connect('admin.pre_execute', array($this, 'addUserToConfig'));

        parent::preExecute();

        $this->dispatcher->connect('admin.build_query', array($this, 'addUserFilter'));
    }

    public function addUserFilter($event, $query) {

        return $query->andWhere($query->getRootAlias() . '.user_id = ? ', $this->getUserIdFromRouteOrSession());
    }

    public function addUserToConfig(sfEvent $event) {
        $this->configuration->setUserId($this->getUserIdFromRouteOrSession());
    }

    protected function getUserIdFromRouteOrSession() {

        $username = $this->getUsernameFromRouteOrSession();

        if ($username == $this->getUser()->getGuardUser()->getUsername()) {
            $user = $this->getUser()->getGuardUser();
        } else {
            $user = Doctrine_Core::getTable('sfGuardUser')->findOneByUsername($username);
        }

        $this->forward404Unless($user);

        return $user->getId();
    }

    public function executeMaxPerPage(sfRequest $request) {

        $form = new PaginationMaxPerPageForm($this->getUser(), $this->getMaxPerPageOptions(), false);

        $isValid = $form->process($request);

        if ($isValid) {

            $this->redirect('@charge?page=1');
        }

        $this->pager = $this->getPager();
        $this->sort = $this->getSort();
        $this->filters_appearance = $this->getFiltersAppearance();

        $this->setTemplate('index');
        $this->pager->form = $form;

        $this->sumAmount = $this->getSumAmount();
    }

    protected function getMaxPerPageOptions() {


        $def = $this->getUser()->getGuardUser()->getListMaxPerPage() ?
                $this->getUser()->getGuardUser()->getListMaxPerPage() :
                $this->configuration->getGeneratorMaxPerPage();


        $options = array(
            'max_per_page_name' => 'charge_list_max_per_page',
            'max_per_page_choices' => array(
                5,
                10,
                20,
                50,
                100,
                150,
                1000,
            ),
            'max_per_page_value' => $def,
        );

        return $options;
    }

    public function executeIndex(sfWebRequest $request) {

        parent::executeIndex($request);

        // filters appearance
        if ($request->getParameter('filters_appearance') && in_array($request->getParameter('filters_appearance'), array('hidden', 'show'))) {
            $this->setFiltersAppearance($request->getParameter('filters_appearance'));
        }

        $this->pager->form = new PaginationMaxPerPageForm($this->getUser(), $this->getMaxPerPageOptions(), false);

        $this->sumAmount = $this->getSumAmount();

        $this->filters_appearance = $this->getFiltersAppearance();
    }

    public function executeFilter(sfWebRequest $request) {

        parent::executeFilter($request);

        $this->pager->form = new PaginationMaxPerPageForm($this->getUser(), $this->getMaxPerPageOptions(), false);

        $this->sumAmount = $this->getSumAmount();
        
        $this->filters_appearance = $this->getFiltersAppearance();
    }

    public function executeNew(sfWebRequest $request) {
        $charge = new Charge();
        $charge->setUserId($this->getUserIdFromRouteOrSession());
        $charge->setDate(date('Y-m-d'));

        $this->form = $this->configuration->getForm($charge);
        $this->charge = $charge;
    }

    public function executeCreate(sfWebRequest $request) {

        $charge = new Charge();
        $charge->setUserId($this->getUserIdFromRouteOrSession());

        $this->form = $this->configuration->getForm($charge);
        $this->charge = $charge;


        $this->processForm($request, $this->form);

        $this->setTemplate('new');
    }

    protected function getSumAmount() {

        // calculating sum of all charges
        $a = clone $this->pager->getQuery();
        $rootAlias = $a->getRootAlias();
        $a->removeDQLqueryPart('limit');
        $a->removeDQLqueryPart('offset');
        $a->addSelect('SUM(' . $rootAlias . '.amount) as sum');

        $r1 = $a->fetchOne();

        if ($r1->getSum()) {
            $r1_sum = $r1->getSum();
        } else {
            $r1_sum = 0;
        }


        // calculating sum of charges of this page
        $b = clone $this->pager->getQuery();
        $rootAlias = $b->getRootAlias();
        $b->addSelect($rootAlias . '.id');

        // MySQL doesn't seems to like subqueries with LIMIT. So we recover ids by executing the subquery separately.
        $charges = $b->execute();
        $ids = array();
        foreach ($charges as $charge) {
            $ids[] = $charge->getId();
        }

        $params = $b->getParams();

        if ($ids) {
            $sum_b = Doctrine_Core::getTable('Charge')->createQuery('b')
                    ->addSelect('b.id, SUM(b.amount) as sum')
                    ->andWhereIn('b.id', $ids);

            $r2 = $sum_b->fetchOne();

            $r2_sum = $r2->getSum();
        } else {
            $r2_sum = 0;
        }


        return array('amount_total' => $r1_sum, 'amount_page' => $r2_sum);
    }

    protected function setFiltersAppearance($appearance) {
        $this->getUser()->setAttribute('charge.filters_appearance', $appearance, 'admin_module');
    }

    protected function getFiltersAppearance() {
        return $this->getUser()->getAttribute('charge.filters_appearance', 'hidden', 'admin_module');
    }

}
