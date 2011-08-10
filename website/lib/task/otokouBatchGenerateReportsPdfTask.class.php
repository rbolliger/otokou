<?php

class otokouBatchGenerateReportsPdfTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            new sfCommandOption('date_from', 'df', sfCommandOption::PARAMETER_OPTIONAL, 'Starting range (date)', null),
            new sfCommandOption('date_to', 'dt', sfCommandOption::PARAMETER_OPTIONAL, 'Ending range (date)', null),
            new sfCommandOption('kilometers_from', 'kf', sfCommandOption::PARAMETER_OPTIONAL, 'Starting range (kilometers)', null),
            new sfCommandOption('kilometers_to', 'kt', sfCommandOption::PARAMETER_OPTIONAL, 'Ending range (kilometers)', null),
        ));

        $this->namespace = 'otokou';
        $this->name = 'batchGenerateReportsPdf';
        $this->briefDescription = 'Generetes Pdf reports for the given range for all users and each active vehicles.';
        $this->detailedDescription = <<<EOF
The [otokou:batchGenerateReportsPdf|INFO] Generetes Pdf reports for the given range for all users and each active vehicles.

Call it with:

  [php symfony otokou:batchGenerateReportsPdf|INFO] [--date_from=[...]] [--date_to=[...]] [--kilometers_from=[...]] [--kilometers_to=[...]]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $this->log('Checking range');

        // checking that the defined range is ok
        $range = $this->checkRange($options);

        // retrieving users list
        $users = $this->getUsers();

        foreach ($users as $user) {

            $this->log('User: ' . $user->getName() . ' (' . $user->getUsername() . ')');
            $this->log('-------------------------------------------------------');

            $vehicles = $this->getActiveVehiclesForUser($user);

            foreach ($vehicles as $vehicle) {

                $this->log('Vehicle: ' . $vehicle->getName());

                $this->generateReportForVehicle($vehicle, $options);

                $this->log(' ');
            }

            $this->log(' ');
        }

        return true;
    }

    protected function checkRange($range = array()) {


        // checking single values
        if (isset($range['date_from'])) {
            $v = new sfValidatorDate();
            $range['date_from'] = $v->clean($range['date_from']);
        }

        if (isset($range['date_to'])) {
            $v = new sfValidatorDate();
            $range['date_to'] = $v->clean($range['date_to']);
        }

        if (isset($range['kilometers_from'])) {
            $v = new sfValidatorInteger(array('min' => 0));
            $range['kilometers_from'] = $v->clean($range['kilometers_from']);
        }

        if (isset($range['kilometers_to'])) {
            $v = new sfValidatorInteger(array('min' => 0));
            $range['kilometers_to'] = $v->clean($range['kilometers_to']);
        }

        // checking ranges
        if (isset($range['date_from']) && isset($range['kilometers_from'])) {
            throw new sfException('Only one between "date_from" and "kilometers_from" can be set.');
        }

        if (isset($range['date_to']) && isset($range['kilometers_to'])) {
            throw new sfException('Only one between "date_to" and "kilometers_to" can be set.');
        }

        $this->log('Range: ');
        if (isset($range['date_from'])) {
            $this->log('  - From ' . $range['date_from']);
        }
        if (isset($range['kilometers_from'])) {
            $this->log('  - From ' . $range['kilometers_from'] . ' km');
        }
        if (isset($range['date_to'])) {
            $this->log('  - To ' . $range['date_to']);
        }
        if (isset($range['kilometers_to'])) {
            $this->log('  - To ' . $range['kilometers_to'] . ' km');
        }
        if (!isset($range['date_from']) && !isset($range['kilometers_from'])) {
            $range['kilometers_from'] = 0;
            $this->log('  - From 0 km');
        }
        
        if (!isset($range['date_to']) && !isset($range['kilometers_to'])) {
            $range['date_to'] = date('Y-m-d');
            $this->log('  - To ' . date('Y-m-d'));
        }
        $this->log(' ');
        
        return $range;
    }

    protected function getUsers() {

        return Doctrine_Core::getTable('sfGuardUser')->findAll();
    }

    protected function getActiveVehiclesForUser(sfGuardUser $user) {

        return Doctrine_Core::getTable('Vehicle')->findArchivedByUserId($user->getId());
    }

    protected function generateReportForVehicle(Vehicle $vehicle, $options = array()) {

        $username = $vehicle->getUser()->getUsername();
        $vehicle_slug = $vehicle->getSlug();
        $from = $this->getFrom($options);
        $to = $this->getTo($options);
        $name = $vehicle->getName() . ' - from ' . $from . ' to ' . $to;

        $arguments = array(
            'username' => $username,
            'vehicles' => $vehicle_slug,
            'name' => '"' . $name . '"',
        );

        $options = array(
            'date_from' => isset($options['date_from']) ? $options['date_from'] : null,
            'date_to' => isset($options['date_to']) ? $options['date_to'] : null,
            'kilometers_from' => isset($options['kilometers_from']) ? $options['kilometers_from'] : null,
            'kilometers_to' => isset($options['kilometers_to']) ? $options['kilometers_to'] : null,
        );

        try {
            $this->runTask('otokou:generateReportPdf', $arguments, $options);
            $this->log('ok: report written.');
            
        } catch (Exception $exc) {
            $this->log('not ok: no charges found');
        }
    }

    protected function getFrom($options) {

        if (isset($options['date_from'])) {
            return $options['date_from'];
        } elseif (isset($options['kilometers_from'])) {
            return $options['kilometers_from'];
        } else {
            return 0;
        }
    }

    protected function getTo($options) {

        if (isset($options['date_to'])) {
            return $options['date_to'];
        } elseif (isset($options['kilometers_to'])) {
            return $options['kilometers_to'];
        } else {
            return date('Y-m-d');
        }
    }

}
