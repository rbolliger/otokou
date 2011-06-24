<?php

/**
 * Vehicle
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    otokou
 * @subpackage model
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Vehicle extends BaseVehicle {

    public function toggleArchive() {
        $this->setIsArchived(!$this->getIsArchived());
        $this->save();
    }

    public function getTraveledDistance() {

        $c = $this->getLastChargeByRange('distance');

        if (!$c) {
            return 0;
        }

        return $c->getKilometers();
    }

    public function getInitialDistance() {

        $c = $this->getFirstChargeByRange('distance');
        if (!$c) {
            return null;
        }

        return $c->getKilometers();
    }

    public function getOverallCost() {

        $charges = $this->getCharges();

        if (!count($charges)) {
            return 0;
        }

        $cost = array();
        foreach ($charges as $c) {
            $cost[] = $c->getAmount();
        }

        return array_sum($cost);
    }

    public function getCostPerKm() {

        $cost = $this->getOverallCost();
        $max_dist = $this->getTraveledDistance();

        if ($max_dist == 0) {
            return null;
        }

        $min_dist = $this->getInitialDistance();
        if (!$min_dist) {
            return null;
        }

        return $cost / ($max_dist - $min_dist);
    }

    public function getAverageConsumption() {

        $charges = $this->getCharges();

        if (!count($charges)) {
            return null;
        }

        $max_dist = $this->getTraveledDistance();
        $min_dist = $this->getInitialDistance();
        if (!$min_dist || $max_dist == 0) {
            return null;
        }


        $fuelId = Doctrine_Core::getTable('Category')->findOneByName('Fuel')->getId();

        $quantity = array();
        foreach ($charges as $c) {

            if ($fuelId === $c->getCategoryId()) {
                $quantity[] = $c->getQuantity();
            }
        }

        if (!count($quantity)) {
            return null;
        }

        return array_sum($quantity) / ($max_dist - $min_dist) * 100;
    }

    public function getFirstChargeByRange($range) {

        return $this->getChargeByRangeAndCriteria($range, array('minOrMax' => 'min'));
    }

    public function getLastChargeByRange($range) {

        return $this->getChargeByRangeAndCriteria($range, array('minOrMax' => 'max'));
    }

    protected function getChargeByRangeAndCriteria($range, $criteria = array()) {

        $ranges = array('date', 'distance');

        if (!in_array($range, $ranges)) {
            throw new Doctrine_Exception('Unknown range ' . $range . ' in ' . __METHOD__);
        }
        $column = $range == 'date' ? 'date' : 'kilometers';

        if (!isset($criteria['minOrMax'])) {
            throw new Doctrine_Exception('minOrMax is required in criteria in ' . __METHOD__);
        }

        $minOrMax = $criteria['minOrMax'];
        if (!in_array($minOrMax, array('min', 'max'))) {
            throw new Doctrine_Exception('Unknown minOrMax value ' . $minOrMax . ' in ' . __METHOD__);
        }

        $q = Doctrine_Core::getTable('Charge')
                ->createQuery('c')
                ->addSelect('c.*')
                ->andWhere('c.vehicle_id = ?', $this->getId());

        $qs = $q->createSubquery()
                ->addFrom('Charge cs')
                ->addSelect($minOrMax . '(cs.' . $column . ')')
                ->andWhere('cs.vehicle_id = ' . $this->getId());

        $q->andWhere('c.' . $column . '= (' . $qs->getDql() . ')');

        return $q->execute()->getFirst();
    }

    public function getOwnReports($max = 5) {

        $q = Doctrine_Query::create()
                ->from('Report r')
                ->leftJoin('r.Vehicles v')
                ->andWhere('v.id = ?', $this->getId())
                ->andWhere('r.num_vehicles = ?', 1)
                ->limit($max);

        return Doctrine_Core::getTable('Report')->getOrderedReports($q);
    }

    public function countReports() {
        $q = Doctrine_Query::create()
                ->from('Report r')
                ->leftJoin('r.Vehicles v')
                ->andWhere('v.id = ?', $this->getId())
                ->andWhere('r.num_vehicles = ?', 1);

        return Doctrine_Core::getTable('Report')->countOrderedReports($q);
    }

}
