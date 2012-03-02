<?php

/**
 * VehicleTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class VehicleTable extends Doctrine_Table {

    /**
     * Returns an instance of this class.
     *
     * @return object VehicleTable
     */
    public static function getInstance() {
        return Doctrine_Core::getTable('Vehicle');
    }

    public function getVehiclesByUserIdQuery($id) {
        $q = $this->createQuery('v')->select('v.*')
                        ->andWhere('v.user_id = ?', $id);

        return $q;
    }

    public function findByUsernameAndSortByArchived($username) {

        $q = $this->createQuery('v');

        $q = $this->addSortByArchivedAndCreatedAt($q);
        $q = $this->addUsernameQuery($q, $username);

        return $q->execute();
    }
  
    public function findByUsername($username) {

        $q = $this->createQuery('v');

        $q = $this->addUsernameQuery($q, $username);
        $q = $this->addSortByName($q);
        $q = $this->addIsArchivedQuery($q, false);

        return $q->execute();
    }
    
    public function countActiveByUserId($user_id) {
        
        $q = $this->createQuery('v');
        
        $q = $this->addUserIdQuery($q, $user_id);
        $q = $this->addIsArchivedQuery($q, false);
        
        return $q->count();
        
    }
    
    
    public function findByUsernameWithNewReports($username) {

        $q = $this->createQuery('v');

        $q = $this->addUsernameQuery($q, $username);
        $q = $this->addSortByName($q);
        $q = $this->addWithNewReportsQuery($q);
        $q = $this->addWithSingleReportQuery($q);
        $q = $this->addIsArchivedQuery($q, false);

        return $q->execute();
    }
    
    public function findActiveByUsernameAndSortByName($params) {
        
        $q = $this->getActiveVehiclesQuery($params['username']);
        
        return $q->execute();
        
    }
    
    public function getActiveVehiclesQuery($username) {
        
        $q = $this->createQuery('v')
                        ->select('v.*');
        
        $q = $this->addUsernameQuery($q, $username);
        $q = $this->addIsArchivedQuery($q,false);        
        $q = $this->addSortByName($q);
        
        return $q;
        
    }

    public function findWithReports($params) {

        $q = $this->createQuery('v')
                        ->select('v.*');

        $q = self::getInstance()->addWithSingleReportQuery($q);

        $q = $this->addSortByArchivedAndCreatedAt($q);

        $q = $this->addUsernameQuery($q, $params['username']);

        return $q->execute();
    }

    public function findBySlugWithReports($params) {

        $q = $this->createQuery('v')
                        ->select('v.*');

        $q = self::getInstance()->addWithSingleReportQuery($q);

        $q = $this->addSortByArchivedAndCreatedAt($q);

        $q = $this->addUsernameQuery($q, $params['username']);

        $q->andWhere('v.slug = ?',$params['slug']);

        return $q->execute();
    }

    protected function addUsernameQuery(Doctrine_Query $q, $username) {

        $root = $q->getRootAlias();

        $q->leftJoin($root . '.User u')
                ->andWhere('u.username = ?', $username);

        return $q;
    }
    
    protected function addUserIdQuery(Doctrine_Query $q, $id) {

        $root = $q->getRootAlias();

        $q->leftJoin($root . '.User u')
                ->andWhere('u.id = ?', $id);

        return $q;
    }
    
    protected function addIsArchivedQuery(Doctrine_Query $q, $isArchived = false) {
        
        $root = $q->getRootAlias();
        
        $q->andWhere($root.'.is_archived = ?',$isArchived);
        
        return $q;
    }
    protected function addSortByArchivedAndCreatedAt(Doctrine_Query $q) {

        $root = $q->getRootAlias();

        return $q->orderBy($root . '.is_archived DESC, ' . $root . '.created_at DESC');
    }
    
    protected function addSortByName(Doctrine_Query $q) {
        
        $root = $q->getRootAlias();

        return $q->orderBy($root . '.name ASC, ' . $root . '.created_at DESC');
    }

    protected function addWithSingleReportQuery(Doctrine_Query $q) {

        $root = $q->getRootAlias();

        $q->innerJoin($root . '.Reports r')
                ->addWhere('r.num_vehicles = 1');

        return $q;
    }
    
    public function findArchivedByUserId($id = null) {
        
        if (!$id) {
            throw new sfDoctrineException('id must be provided');
        }
        
        return $this->createQuery('v')
                ->addWhere('v.user_id = ?', $id)
                ->andWhere('v.is_archived = ?', false)
                ->leftJoin('v.User u')
                ->execute();
        
    }
    
    protected function addWithNewReportsQuery(Doctrine_Query $q) {
        
        $alias = $q->getRootAlias();
        
        $q->innerJoin($alias.'.Reports r2')
                ->addWhere('r2.is_new = ?',true);
        
        return $q;
        
    }
    

}