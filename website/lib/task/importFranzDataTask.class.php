<?php

class importFranzDataTask extends sfBaseTask {

    protected function configure() {
        // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('file', sfCommandArgument::REQUIRED, 'Name of the csv file to import'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            new sfCommandOption('fixture', null, sfCommandOption::PARAMETER_OPTIONAL, 'The relative fixture file (relative path with respect to sf_data_dir)'),
            new sfCommandOption('User', null, sfCommandOption::PARAMETER_REQUIRED, 'The identifier of the User in fixtures', 'franz'),
            new sfCommandOption('Vehicle', null, sfCommandOption::PARAMETER_REQUIRED, 'The identifier of the Vehicle in fixtures', 'astra'),
        ));

        $this->namespace = 'import';
        $this->name = 'FranzData';
        $this->briefDescription = 'Imports data from csv files generated from Franz\'s excel and generates a fixture file';
        $this->detailedDescription = <<<EOF
The [FranzData|INFO] task imports data from a csv file to a fixture file. The csv file has been generated from
Franz vehicles data.

Call it with:

  [php symfony import:FranzData|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();


        // Checking that the csv file exist
        $file = sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . $arguments['file'];

        if (!file_exists($file)) {
            throw new sfException(sprintf('File %s does not exist.', $file));
        }

        // building fixture file name and path

        $user = isset($options['User']) ? $options['User'] : 'franz';
        $vehicle = isset($options['Vehicle']) ? $options['Vehicle'] : 'astra';


        $fixture_file = isset($options['fixture']) ? $options['fixture'] : $this->getFixtureFile('11_' . $user . '_' . $vehicle);
        $fixture_file = sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . $fixture_file;

        if (file_exists($fixture_file)) {
            $answer = $this->askConfirmation(sprintf('A yaml fixture already exist in "%s". Overwrite? [Y/n]', $fixture_file));

            if (!$answer) {
                return false;
            }
        }



        $file_handle = fopen($file, "r");

        $fixtures = array();
        $id = 0;

        while (!feof($file_handle)) {

            $line_of_text = fgetcsv($file_handle, 1024);


            $s = $this->importLine($line_of_text, $options);
            if (!$s) {
                continue;
            }

            $id++;
            $fname = $s['User'] . '_' . $s['Vehicle'] . '_' . $id;

            $fixtures[$fname] = $s;

            $this->log(sprintf('Read line %d', $id));
        }


        fclose($file_handle);

        // Writing yaml file

        $fixtures = array('Charge' => $fixtures);

        $dumper = new sfYamlDumper();
        $yaml = $dumper->dump($fixtures, 3);

        file_put_contents($fixture_file, $yaml);

        $this->log(sprintf('OK fixture file successfully created in %s.', $fixture_file));
    }

    protected function importLine($line, $options) {

        if (!$line) {
            return '';
        }

        $user = isset($options['User']) ? $options['User'] : 'franz';
        $vehicle = isset($options['Vehicle']) ? $options['Vehicle'] : 'astra';


        $c = array();
        $c['User'] = $user;
        $c['Vehicle'] = $vehicle;
        $c['date'] = $this->formatDate($line[0]);
        $c['comment'] = $line[1];
        $c['kilometers'] = $this->toNumber($line[2]);


        if ($this->toNumber($line[3]) > 0) {
            $amount = $this->toNumber($line[3]);
        } elseif ($this->toNumber($line[4]) > 0) {
            $amount = $this->toNumber($line[4]);
        } elseif ($this->toNumber($line[3]) == 0 && $this->toNumber($line[4]) == 0) {
            $amount = 0;
        } else {
            throw new sfException(sprintf('Amount defined at line "%s" is not an operating or investment cost. Something wrong here.', implode(', ', $line)));
        }

        $c['amount'] = $amount;


        $category = $this->parseCategory($line[1]);

        $c['Category'] = $category;


        if ('fuel' === $category) {

            $q = $this->toNumber($line[5]);

            if (!$q) {
                throw new sfException(sprintf('Line "%s" appears to be in category fuel, but no quantity is defined.', implode(', ', $line)));
            }

            $c['quantity'] = $q;
        }

        return $c;
    }

    protected function parseCategory($param) {

        if (!$param) {
            throw new sfException('Empty entry parameter. Something wrong here');
        }


        if ($this->contains($param, array(
                    'Acquisto',
                    'Ripresa',
                    'Vendita',
                ))) {

            return 'purchase';
        }

        if ($this->contains($param, array(
                    'Rata'
                ))) {

            return 'leasing';
        }

        if ($this->contains($param, array(
                    'circolazione',
                    'tass',
                    'vignetta',
                    'collaudo',
                    'CO',
                ))) {

            return 'taxes';
        }

        if ($this->contains($param, array(
                    'servizio',
                    'lavaggio',
                    'cambio',
                    'olio',
                    'sosti',
                    'ammortizzatore',
                    'lampadin',
                    'coprimozzo',
                    'alternatore',
                    'IVA',
                    'preparazione',
                    'montato',
                    'controllo',
                    'blocco',
                    'carroz',
                    'vari',
                    'riparazion',
                    'guarniz',
                    'cinghia',
                    'freni',
                    'marmitta',
                    'bilanciamento',
                    'camera',
                    'calotta',
                    'liquido',
                    'candele',
                ))) {

            return 'maintenance';
        }

        if ($this->contains($param, array(
                    'Benzina'
                ))) {

            return 'fuel';
        }

        if ($this->contains($param, array(
                    'gomm'
                ))) {

            return 'accessories';
        }

        if ($this->contains($param, array(
                    'Assicurazione',
                    'Asicurazione',
                ))) {

            return 'insurance';
        }

        if ($this->contains($param, array(
                    'Multa'
                ))) {

            return 'fines';
        }


        throw new sfException(sprintf('Cannot find any category for "%s"', $param));
    }

    protected function contains($subject, $keywords) {

        $pattern = '';
        foreach ($keywords as $key => $kw) {
            $pattern .= ( $key == 0 ? '' : '|') . $kw;
        }

        $pattern = '/' . $pattern . '/i';


        return preg_match($pattern, $subject);
    }

    protected function toNumber($string) {

        $n = str_replace(',', '', $string);
        $n = str_replace('-', '0', $n);

        $n = (float) $n;

        return $n;
    }

    protected function getFixtureFile($filename) {

        $fixture = 'fixtures' . DIRECTORY_SEPARATOR . $filename . '.yml';


        return $fixture;
    }

    protected function formatDate($date) {

        $dto = new DateTime($date);
        $formatted = $dto->format('Y-m-d');

        return $formatted;
    }

}

