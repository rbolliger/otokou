<?php

class reportComponents extends sfComponents {

    public function executeReportsMenu() {

        $username = $this->getUser()->getGuardUser()->getUsername();

        $this->vehicles = Doctrine::getTable('Vehicle')
                        ->findByUsernameAndSortByArchived($username);
    }

}
