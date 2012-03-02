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

    public function executeMaxPerPage(sfRequest $request) {

        parent::executeMaxPerPage($request);

        $this->to_slots = $this->getToSlots();
    }

    public function executeIndex(sfWebRequest $request) {

        parent::executeIndex($request);

        $this->to_slots = $this->getToSlots();
    }

    public function executeFilter(sfWebRequest $request) {

        parent::executeFilter($request);

        $this->to_slots = $this->getToSlots();
    }

    public function executeNew(sfWebRequest $request) {

        if (!Doctrine_Core::getTable('Vehicle')->countActiveByUserId($this->getUserIdFromRouteOrSession())) {
            $this->redirect('@charge_no_vehicle');
        }

        parent::executeNew($request);

        $this->charge->setDate(date('Y-m-d'));
        $this->form = $this->configuration->getForm($this->charge);
    }

    public function executeNoVehicle(sfWebRequest $request) {

        $v = new Vehicle();
        $v->setUserId($this->getUserIdFromRouteOrSession());

        $this->form = new VehicleForm($v);
    }

    public function executeAddVehicle(sfWebRequest $request) {

        $v = new Vehicle();
        $v->setUserId($this->getUserIdFromRouteOrSession());

        $this->form = new VehicleForm($v);

        $this->processVehicleForm($request, $this->form);

        $this->setTemplate('noVehicle');
    }

    protected function processVehicleForm(sfWebRequest $request, VehicleForm $form) {

        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {

            try {
                $vehicle = $form->save();
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

            $this->redirect(array('sf_route' => 'charge_new'));
        } else {
            $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
        }
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

    public function getToSlots() {
        return array(
            'leftcol' => $this->getSumAmount(),
        );
    }

}
