<?php

class chartSourceUtilityTest {

    public function getChartSource($vd, $cd) {

        $scn = $this->getCase($vd, $cd);

        switch ($scn) {
            case 1:

                $q1 = Doctrine_Core::getTable('Charge')->createQuery('c')
                                ->select('c.*')
                                ->leftJoin('c.User u')
                                ->andWhere('u.Username = ?', array('user_gs'))
                                ->execute();

                $series = array(new ChartDataSerie(array('raw_data' => $q1, 'label' => 'all stacked', 'id' => 'allstacked')));
                break;

            case 2:

                $q1 = Doctrine_Core::getTable('Charge')->createQuery('c')
                                ->select('c.*')
                                ->leftJoin('c.Category ct')
                                ->andWhere('ct.Name = ?', array('Tax'))
                                ->leftJoin('c.User u')
                                ->andWhere('u.Username = ?', array('user_gs'))
                                ->execute();


                $q2 = Doctrine_Core::getTable('Charge')->createQuery('c')
                                ->select('c.*')
                                ->leftJoin('c.Category ct')
                                ->andWhere('ct.Name = ?', array('Fuel'))
                                ->leftJoin('c.User u')
                                ->andWhere('u.Username = ?', array('user_gs'))
                                ->execute();

                $series = array(
                    new ChartDataSerie(array('raw_data' => $q1, 'label' => 'tax', 'id' => 'tax')),
                    new ChartDataSerie(array('raw_data' => $q2, 'label' => 'fuel', 'id' => 'fuel')),
                );
                break;

            case 3:

                $q1 = Doctrine_Core::getTable('Charge')->createQuery('c')
                                ->select('c.*')
                                ->leftJoin('c.Vehicle v')
                                ->andWhere('v.Name = ?', array('car_gs_1'))
                                ->leftJoin('c.User u')
                                ->andWhere('u.Username = ?', array('user_gs'))
                                ->execute();


                $q2 = Doctrine_Core::getTable('Charge')->createQuery('c')
                                ->select('c.*')
                                ->leftJoin('c.Vehicle v')
                                ->andWhere('v.Name = ?', array('car_gs_2'))
                                ->leftJoin('c.User u')
                                ->andWhere('u.Username = ?', array('user_gs'))
                                ->execute();

                $series = array(
                    new ChartDataSerie(array('raw_data' => $q1, 'label' => 'car_gs_1', 'id' => 'car_gs_1')),
                    new ChartDataSerie(array('raw_data' => $q2, 'label' => 'car_gs_2', 'id' => 'car_gs_2')),
                );
                break;

            case 4:

                $q1 = Doctrine_Core::getTable('Charge')->createQuery('c')
                                ->select('c.*')
                                ->leftJoin('c.Vehicle v')
                                ->andWhere('v.Name = ?', array('car_gs_1'))
                                ->leftJoin('c.Category ct')
                                ->andWhere('ct.Name = ?', array('Tax'))
                                ->leftJoin('c.User u')
                                ->andWhere('u.Username = ?', array('user_gs'))
                                ->execute();


                $q2 = Doctrine_Core::getTable('Charge')->createQuery('c')
                                ->select('c.*')
                                ->leftJoin('c.Vehicle v')
                                ->andWhere('v.Name = ?', array('car_gs_1'))
                                ->leftJoin('c.Category ct')
                                ->andWhere('ct.Name = ?', array('Fuel'))
                                ->leftJoin('c.User u')
                                ->andWhere('u.Username = ?', array('user_gs'))
                                ->execute();

                $q3 = Doctrine_Core::getTable('Charge')->createQuery('c')
                                ->select('c.*')
                                ->leftJoin('c.Vehicle v')
                                ->andWhere('v.Name = ?', array('car_gs_2'))
                                ->leftJoin('c.Category ct')
                                ->andWhere('ct.Name = ?', array('Tax'))
                                ->leftJoin('c.User u')
                                ->andWhere('u.Username = ?', array('user_gs'))
                                ->execute();


                $q4 = Doctrine_Core::getTable('Charge')->createQuery('c')
                                ->select('c.*')
                                ->leftJoin('c.Vehicle v')
                                ->andWhere('v.Name = ?', array('car_gs_2'))
                                ->leftJoin('c.Category ct')
                                ->andWhere('ct.Name = ?', array('Fuel'))
                                ->leftJoin('c.User u')
                                ->andWhere('u.Username = ?', array('user_gs'))
                                ->execute();

                $series = array(
                    new ChartDataSerie(array('raw_data' => $q1, 'label' => 'car_gs_1_tax', 'id' => 'car_gs_1_tax')),
                    new ChartDataSerie(array('raw_data' => $q2, 'label' => 'car_gs_1_fuel', 'id' => 'car_gs_1_fuel')),
                    new ChartDataSerie(array('raw_data' => $q3, 'label' => 'car_gs_2_tax', 'id' => 'car_gs_2_tax')),
                    new ChartDataSerie(array('raw_data' => $q4, 'label' => 'car_gs_2_fuel', 'id' => 'car_gs_2_fuel')),
                );
                break;

            default:
                throw new sfException('Case not defined');
                break;
        }



        $g = new ChartSource();
        $g->setSeries($series);

        return $g;
    }

    public function getXForScenario($scenario) {

        $case = $this->getCase($scenario[0], $scenario[1]);
        $range = $scenario[2];

//        switch ($case) {
//            case 1:

        if ($range == 'distance') {
            $x = array(
                12,
                50,
                65,
                70,
                100,
                123,
                200,
                300,
                324,
                400,
                456,
                654
            );
        } else {
            $x = array(
                1293836400,
                1294095600,
                1294614000,
                1296514800,
                1296946800,
                1297724400,
                1298934000,
                1299625200,
                1300489200,
                1301608800,
                1304200800,
                1306879200,
            );
        }

        return $x;
    }

    public function getCase($vd, $cd) {

        if ($vd == 'stacked' && $cd == 'stacked') {
            $scn = 1;
        } elseif ($vd == 'stacked' && $cd == 'single') {
            $scn = 2;
        } elseif ($vd == 'single' && $cd == 'stacked') {
            $scn = 3;
        } elseif ($vd == 'single' && $cd == 'single') {
            $scn = 4;
        }

        return $scn;
    }

    public function runTest($t,$scenario, $fname, $y) {


        $t->diag(sprintf('->%s() scenario %d (%s)', $fname, $key, implode(', ', $scenario)));
        $g = $this->getChartSource($scenario[0], $scenario[1]);
        $data = $g->$fname($scenario[2]);
        $x = $this->getXForScenario($scenario);

        $t->cmp_ok(array_values($data['x']['values']), '==', $x, sprintf('->%s() x-values are ok', $fname));
        $t->cmp_ok(count($data['y']['series']), '===', count($y), sprintf('->%s() y-values series count ok', $fname));

        foreach ($data['y']['series'] as $ykey => $values) {
            $t->cmp_ok(array_values($data['y']['series'][$ykey]['values']), '==', $y[$ykey], sprintf('->%s() y-values for serie "%d" ok', $fname, $ykey));
        }
    }

}
