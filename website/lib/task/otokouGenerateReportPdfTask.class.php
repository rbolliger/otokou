<?php

class otokouGenerateReportPdfTask extends sfBaseTask {

    protected function configure() {

        $this->addArguments(array(
            new sfCommandArgument('username', sfCommandArgument::REQUIRED, 'Username'),
            new sfCommandArgument('vehicles', sfCommandArgument::REQUIRED, 'List of vehicles slugs, separated by commas (",")'),
            new sfCommandArgument('name', sfCommandArgument::REQUIRED, 'Name of the report'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', 'app', sfCommandOption::PARAMETER_OPTIONAL, 'Application', 'frontend'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_OPTIONAL, 'The connection name', 'doctrine'),
            new sfCommandOption('date_from', 'df', sfCommandOption::PARAMETER_OPTIONAL, 'Starting range (date)', null),
            new sfCommandOption('date_to', 'dt', sfCommandOption::PARAMETER_OPTIONAL, 'Ending range (date)', null),
            new sfCommandOption('kilometers_from', 'kf', sfCommandOption::PARAMETER_OPTIONAL, 'Starting range (kilometers)', null),
            new sfCommandOption('kilometers_to', 'kt', sfCommandOption::PARAMETER_OPTIONAL, 'Ending range (kilometers)', null),
        ));

        $this->namespace = 'otokou';
        $this->name = 'generateReportPdf';
        $this->briefDescription = 'Generates a pdf report for the requested user, vehicle(s) and date/kilometers range.';
        $this->detailedDescription = <<<EOF
The [otokou:generateReportPdf|INFO] task generates a pdf report for the requested user, vehicle(s) and date/kilometers range.

Vehicles may be specified as comma-separated values, like: "car1, car2, car3", where car1, car2 and car3 are the slugs of the vehicles. 
All the specified vehicles must be owned by the user defined as input.

A limiting range can be specified by defining a starting and an ending point. These values may be dates or kilometers.
Only one starting point and an ending point may be specified. If the starting point is not specified, 0 km are assumed. 
If the ending point is not specified, todays date is assumed.

Call the task with:

  [php symfony otokou:generateReportPdf username vehicles name [--date_from (-df)] [--date_to (-dt)] [--kilometers_from (-kf)] [--kilometers_to (-kt)]|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {


        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();


        // create a Report object
        $report = $this->createReportObject($arguments, $options);


        // generate the report
        $status = $this->createReportPdf($report, $arguments, $options);

        return $status;
    }

    protected function createReportObject($arguments = array(), $options = array()) {

        $this->log('Creating a new Report entry in DB...');

        $user = Doctrine_Core::getTable('sfGuardUser')->findOneByUsername($arguments['username']);
        if (!$user) {
            throw new sfException('Cannot find any user with username "' . $arguments['username'] . '"');
        }
        
        $r = new Report();
        $r->setUser($user);
        
        $form = new ReportForm($r);

        $data = $this->prepareDataForForm($form, $arguments, $options);

        $this->processForm($form, $data);

        $this->log('...done');

        return $form->getObject();
    }

    protected function createReportPdf(Report $report, $arguments = array(), $options = array()) {

        $this->log('Generating Pdf Report...');

        // create a context, and load the helper
        //$configuration = ProjectConfiguration::getApplicationConfiguration( $options['application'], $options['env'], true);
        $configuration = $this->configuration;
        $context = sfContext::createInstance($configuration);
        $configuration->loadHelpers('Partial');

        // generate HTML part
        $context->getRequest()->setRequestFormat('html');


        $status = $report->generatePdf($context, 'ChartBuilderPChart', $report->getPdfFileFullPath());

        if ($status) {

            $this->log('...done. File saved in ' . $report->getPdfFileFullPath());
        } else {
            
            $this->log('...failed. Report has not been generated because no charges have been registered for the requested range.');
        }
        
        return $status;
    }

    protected function prepareDataForForm(sfForm $form, $arguments = array(), $options = array()) {

        $data = array(
            'user_id' => $form->getObject()->getUserId(),
            'name' => $arguments['name'],
            'vehicles_list' => $this->parseVehicles($arguments['vehicles']),
            'date_range' => array(
                'from' => isset($options['date_from']) ? $options['date_from'] : null,
                'to' => isset($options['date_to']) ? $options['date_to'] : null,
            ),
            'kilometers_range' => array(
                'from' => isset($options['kilometers_from']) ? $options['kilometers_from'] : null,
                'to' => isset($options['kilometers_to']) ? $options['kilometers_to'] : null,
            ),
            $form->getCSRFFieldName() => $form->getCSRFToken(),
        );

        return $data;
    }

    protected function processForm(sfForm $form, $data = array()) {


        $form->bind($data);

        if ($form->isValid()) {

            try {
                $report = $form->save();

                return $report;
            } catch (Doctrine_Validator_Exception $e) {

                $errorStack = $form->getObject()->getErrorStack();

                $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ? 's' : null) . " with validation errors: ";
                foreach ($errorStack as $field => $errors) {
                    $message .= "$field (" . implode(", ", $errors) . "), ";
                }
                $message = trim($message, ', ');

                throw new sfException($message);
            }
        } else {

            if ($es = $form->getErrorSchema()) {
                throw new sfException($es->getMessage());
            }

            throw new sfException('The item has not been saved due to some errors.');
        }
    }

    protected function parseVehicles($vehiclesString = '') {

        $vehicles_slugs = preg_split('/,\s*/', $vehiclesString);


        $ids = array();

        foreach ($vehicles_slugs as $slug) {
            $vehicle = Doctrine_Core::getTable('Vehicle')->findOneBySlug($slug);

            if (!$vehicle) {
                throw new sfException(sprintf('Cannot find any vehicle with slug "%s"', $slug));
            }

            $ids[] = $vehicle->getId();
        }


        return $ids;
    }

}

/*
  protected function execute($arguments = array(), $options = array())
  {

 
    // create a context, and load the helper
    $context = sfContext::createInstance($this->configuration);
    $this->configuration->loadHelpers('Partial');
 
    // create the message
    $message = $this->getMailer()->compose('no-reply@domain.com', 'me@domain.com', 'Subject Line');
 
    // generate HTML part
    $context->getRequest()->setRequestFormat('html');
    $html  = get_partial('radius/overusage', array('results' => $results));
    $message->setBody($html, 'text/html');
 
    // generate plain text part
    $context->getRequest()->setRequestFormat('txt');
    $plain = get_partial('radius/overusage', array('results' => $results));
    $message->addPart($plain, 'text/plain');
 
    // send the message
    $this->getMailer()->send($message);
  }
 * 
 * 
 */