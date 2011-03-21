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
                // add your own options here
        ));

        $this->namespace = 'import';
        $this->name = 'FranzData';
        $this->briefDescription = 'Imports data from csv files generated from Franz\'s excel data into the DB';
        $this->detailedDescription = <<<EOF
The [FranzData|INFO] task imports data from a csv file to the database. The csv file has been generated from
Franz vehicles data.

Call it with:

  [php symfony import:FranzData|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $file = sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . $arguments['file'];

        if (!file_exists($file)) {
            throw new sfException(sprintf('File %s does not exist.', $file));
        }



        $file_handle = fopen($file, "r");

        while (!feof($file_handle)) {

            $line_of_text = fgetcsv($file_handle, 1024);

            $s = $this->importLine($line_of_text);

            echo $s . "\n";
        }

        fclose($file_handle);
    }

    protected function importLine($line) {

        if (!$line) {
            return '';
        }


        $c = new Charge();

        $c->setUser($this->getUser('franz'));
        $c->setVehicle($this->getVehicle('Opel Astra Caravan 1.6'));
        $c->setDate($line[0]);
        $c->setComment($line[1]);
        $c->setKilometers($line[2]);

        if ($n3 = $this->toNumber($line[3]) > 0) {
            $amount = $n3;
        } elseif ($n4 = $this->toNumber($line[4]) > 0) {
            $amount = $n4;
        }elseif ($n3 == 0 && $n4 == 0) {
            $amount = 0;
        } else {
            throw new sfException(sprintf('Amount defined at line "%s" is not an operating or investment cost. Something wrong here.', implode(', ', $line)));
        }


        $c->setAmount($amount);

        $category = $this->parseCategory($line[1]);

        $c->setCategory($category);

        if ($this->getCategory('Fuel') === $category) {
            $q = $this->toNumber($line[5]);

            if (!$q) {
                throw new sfException(sprintf('Line "%s" appears to be in category fuel, but no quantity is defined.',implode(', ', $line)));
            }

            $c->setQuantity($q);
        }



        $s = '';
        $s .= $c->getVehicle()->getName().', ';
        $s .= $c->getUser()->getUsername().', ';
        $s .= $c->getCategory()->getName().', ';
        $s .= $c->getDate().', ';
        $s .= $c->getKilometers().', ';
        $s .= $c->getAmount().', ';
        $s .= $c->getQuantity();

        return $s;
    }

    protected function getUser($username) {

        $u = Doctrine_Core::getTable('sfGuardUser')->findOneByUsername($username);

        if (!$u) {
            throw new sfException(sprintf('User "%s" not found',$username));
        }

        return $u;
    }

    protected function getVehicle($name) {

        $v = Doctrine_Core::getTable('Vehicle')->findOneByName($name);

        if (!$v) {
            throw new sfException(sprintf('Vehicle "%s" not found',$name));
        }

        return $v;
    }

    protected function getCategory($name) {

        $v = Doctrine_Core::getTable('Category')->findOneByName($name);

        if (!$v) {
            throw new sfException(sprintf('Category "%s" not found',$name));
        }

        return $v;
    }

    protected function parseCategory($param) {

        if (!$param) {
            throw new sfException('Empty entry parameter. Something wrong here');
        }


        if ($this->contains($param,array(
            'Acquisto',
            'Ripresa',
            'Vendita',
            ))) {
            return $this->getCategory('Initial investment');
        }

        if ($this->contains($param,array(
            'Rata'
            ))) {
            return $this->getCategory('Leasing');
        }

        if ($this->contains($param,array(
            'circolazione',
            'tass',
            'vignetta'
            ))) {
            return $this->getCategory('Tax');
        }

        if ($this->contains($param,array(
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
            ))) {
            return $this->getCategory('Maintenance');
        }

        if ($this->contains($param,array(
            'Benzina'
            ))) {
            return $this->getCategory('Fuel');
        }

        if ($this->contains($param,array(
            'gomm'
            ))) {
            return $this->getCategory('Accessory');
        }

        if ($this->contains($param,array(
            'Assicurazione',
            'Asicurazione',
            ))) {
            return $this->getCategory('Insurance');
        }

        if ($this->contains($param,array(
            'Multa'
        ))) {
            return $this->getCategory('Fine');
        }


        throw new sfException(sprintf('Cannot find any category for "%s"',$param));
        

    }

    protected function contains($subject,$keywords) {

        $pattern = '';
        foreach ($keywords as $key => $kw) {
            $pattern .= ($key == 0 ? '' : '|').$kw;
        }

        $pattern = '/'.$pattern.'/i';


        return preg_match($pattern, $subject);

    }

    protected function toNumber($string) {

        $n = str_replace(',', '', $string);
        $n = str_replace('-', '0', $string);

        $n = (float) $n;

        return $n;

    }

}

