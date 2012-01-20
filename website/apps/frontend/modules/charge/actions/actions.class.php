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

        $this->sumAmount = $this->getSumAmount();
    }

    public function executeIndex(sfWebRequest $request) {

        parent::executeIndex($request);

        $this->sumAmount = $this->getSumAmount();
    }

    public function executeFilter(sfWebRequest $request) {

        parent::executeFilter($request);

        $this->sumAmount = $this->getSumAmount();
    }

    public function executeNew(sfWebRequest $request) {

        parent::executeNew($request);

        $this->charge->setDate(date('Y-m-d'));
        $this->form = $this->configuration->getForm($this->charge);
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

}
