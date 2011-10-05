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
    
    protected $charge_bouds = array();

    public function toggleArchive() {
        $this->setIsArchived(!$this->getIsArchived());
        $this->save();
    }

    public function getTraveledDistance() {  
        
        $id = $this->getInitialDistance();
        $fd = $this->getFinalDistance();
        
        if (!$id || !$fd) {
            
            return 0;
        }
        

        return $fd -  $id;
    }

    public function getInitialDistance() {

        $c = $this->getFirstChargeByRange('distance');
        if (!$c) {
            return null;
        }

        return $c->getKilometers();
    }
    
    public function getFinalDistance() {

       $lc = $this->getLastChargeByRange('distance');

        if (!$lc) {
            return null;
        }
        
        return $lc->getKilometers();
        
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
        
        $td = $this->getTraveledDistance();

        if ($td == 0) {
            return null;
        }

       

        return $cost / $td;
    }

    public function getAverageConsumption() {

        $charges = $this->getCharges();

        if (!count($charges)) {
            return null;
        }
        
        $td = $this->getTraveledDistance();
        
        if (!$td) {
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

        return array_sum($quantity) / $td * 100;
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
               
        if (isset($this->charge_bouds[$column][$minOrMax])) {
            
            return $this->charge_bouds[$column][$minOrMax];
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
        
        $res = $q->execute()->getFirst();
        
        $this->charge_bouds[$column][$minOrMax] = $res;
        

        return $res;
    }

    public function getOwnReports($max = 5) {

        $q = $this->getOwnReportsQuery()
                ->limit($max);

        return $q->execute();
    }

    public function countReports() {
        
        $q = $this->getOwnReportsQuery();

        return $q->count();
    }

    public function getOwnReportsQuery() {

        $q = Doctrine_Query::create()
                ->from('Report r')
                ->leftJoin('r.Vehicles v')
                ->andWhere('v.id = ?', $this->getId())
                ->andWhere('r.num_vehicles = ?', 1);
        
        return Doctrine_Core::getTable('Report')->addOrderedReportsQuery($q);
    }

}
