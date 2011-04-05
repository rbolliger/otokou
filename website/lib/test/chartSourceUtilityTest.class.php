<?php

class chartSourceUtilityTest extends otokouTestFunctional {

    public function getChartSource($vd, $cd) {

        $scn = $this->getCase($vd, $cd);

        switch ($scn) {
            case 1:

                $q1 = Doctrine_Core::getTable('Charge')->createQuery('c')
                                ->select('c.*')
                                ->leftJoin('c.User u')
                                ->andWhere('u.Username = ?', array('user_gs'))
                                ->execute();

                $v = Doctrine_Core::getTable('Vehicle')->findByUserId($this->getUserId('user_gs'));
                $vid = $this->extractIds($v);

                $c = Doctrine_Core::getTable('Category')->findAll();
                $cid = $this->extractIds($c);

                $series = array(new ChartDataSerie(array(
                        'raw_data' => $q1,
                        'label' => 'all stacked',
                        'id' => 'allstacked',
                        'vehicle_id' => $vid,
                        'category_id' => $cid,
                    )));
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

                $v = Doctrine_Core::getTable('Vehicle')->findByUserId($this->getUserId('user_gs'));
                $vid = $this->extractIds($v);

                $c1 = Doctrine_Core::getTable('Category')->findOneByName('Tax');
                $cid1 = $this->extractIds($c1);

                $c2 = Doctrine_Core::getTable('Category')->findOneByName('Fuel');
                $cid2 = $this->extractIds($c2);

                $series = array(
                    new ChartDataSerie(array(
                        'raw_data' => $q1,
                        'label' => 'tax',
                        'id' => 'tax',
                        'vehicle_id' => $vid,
                        'category_id' => $cid1,
                    )),
                    new ChartDataSerie(array(
                        'raw_data' => $q2,
                        'label' => 'fuel',
                        'id' => 'fuel',
                        'vehicle_id' => $vid,
                        'category_id' => $cid2,
                    )),
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

                $v1 = Doctrine_Core::getTable('Vehicle')->findOneByName('car_gs_1');
                $vid1 = $this->extractIds($v1);

                $v2 = Doctrine_Core::getTable('Vehicle')->findOneByName('car_gs_2');
                $vid2 = $this->extractIds($v2);

                $c = Doctrine_Core::getTable('Category')->findAll();
                $cid = $this->extractIds($c);

                $series = array(
                    new ChartDataSerie(array(
                        'raw_data' => $q1,
                        'label' => 'car_gs_1',
                        'id' => 'car_gs_1',
                        'vehicle_id' => $vid1,
                        'category_id' => $cid,
                    )),
                    new ChartDataSerie(array(
                        'raw_data' => $q2,
                        'label' => 'car_gs_2',
                        'id' => 'car_gs_2',
                        'vehicle_id' => $vid2,
                        'category_id' => $cid,
                    )),
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

                $v1 = Doctrine_Core::getTable('Vehicle')->findOneByName('car_gs_1');
                $vid1 = $this->extractIds($v1);

                $v2 = Doctrine_Core::getTable('Vehicle')->findOneByName('car_gs_2');
                $vid2 = $this->extractIds($v2);

                $c1 = Doctrine_Core::getTable('Category')->findOneByName('Tax');
                $cid1 = $this->extractIds($c1);

                $c2 = Doctrine_Core::getTable('Category')->findOneByName('Fuel');
                $cid2 = $this->extractIds($c2);


                $series = array(
                    new ChartDataSerie(array(
                        'raw_data' => $q1,
                        'label' => 'car_gs_1_tax',
                        'id' => 'car_gs_1_tax',
                        'vehicle_id' => $vid1,
                        'category_id' => $cid1,
                    )),
                    new ChartDataSerie(array(
                        'raw_data' => $q2,
                        'label' => 'car_gs_1_fuel',
                        'id' => 'car_gs_1_fuel',
                        'vehicle_id' => $vid1,
                        'category_id' => $cid2,
                    )),
                    new ChartDataSerie(array(
                        'raw_data' => $q3,
                        'label' => 'car_gs_2_tax',
                        'id' => 'car_gs_2_tax',
                        'vehicle_id' => $vid2,
                        'category_id' => $cid1,
                    )),
                    new ChartDataSerie(array(
                        'raw_data' => $q4,
                        'label' => 'car_gs_2_fuel',
                        'id' => 'car_gs_2_fuel',
                        'vehicle_id' => $vid2,
                        'category_id' => $cid2,
                    )),
                );
                break;

            default:
                throw new sfException('Case not defined');
                break;
        }


        $params = array(
            'vehicle_display' => $vd,
            'category_display' => $cd,
        );

        $g = new ChartSource();
        $g->addParams($params);
        $g->setSeries($series);


        return $g;
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

    public function runTest($t, $scenario, $fname, $x, $y, $options = array()) {


        $t->diag(sprintf('->%s() scenario (%s)', $fname, implode(', ', $scenario)));
        $g = $this->getChartSource($scenario[0], $scenario[1]);

        // For some scenarios, the function may not work. This code tests that.
        if (false === $y) {

            try {
                $data = $this->callMethod($g, $fname, $options);
                $t->fail('no code should be executed after throwing an exception');
            } catch (Exception $e) {
                $t->pass(sprintf('This scenario is not accepted by %s', $fname));
            }

            return array();
        }


        $data = $this->callMethod($g, $fname, $options);


        $t->ok(isset($data['x']), sprintf('->%s() returns a "x" field', $fname));
        $t->ok(isset($data['x']['id']), sprintf('->%s() returns an id for "x" field', $fname));
        $t->ok(isset($data['x']['values']), sprintf('->%s() returns values for "x" field', $fname));
        $t->ok(isset($data['x']['description']), sprintf('->%s() returns a description for "x" field', $fname));

        $t->cmp_ok(array_values($data['x']['values']), '==', $x, sprintf('->%s() x-values are ok', $fname));

        $t->ok(isset($data['y']), sprintf('->%s() returns a "y" field', $fname));
        $t->ok(isset($data['y']['series']), sprintf('->%s() returns a series array for "y" field', $fname));
        $t->ok(isset($data['y']['description']), sprintf('->%s() returns a description for "x" field', $fname));

        $t->cmp_ok(count($data['y']['series']), '===', count($y), sprintf('->%s() y-values series count ok', $fname));

        foreach ($data['y']['series'] as $key => $serie) {

            $t->ok(isset($serie['id']), sprintf('->%s() serie %d has an "id"', $fname, $key));
            $t->ok(isset($serie['label']), sprintf('->%s() serie %d has a "label"', $fname, $key));
            $t->ok(isset($serie['values']), sprintf('->%s() serie %d has some "values"', $fname, $key));

            $t->cmp_ok(array_values($data['y']['series'][$key]['values']), '==', $y[$key], sprintf('->%s() y-values for serie "%d" ok', $fname, $key));
        }

        return $data;
    }

    private function extractIds($coll) {

        if (is_a($coll, 'Doctrine_Collection')) {

            $id = array();
            foreach ($coll as $el) {
                $id[] = $el->getId();
            }
        } else {
            $id = $coll->getId();
        }

        return $id;
    }

    private function callMethod($class, $method, $options) {

        if (!$options) {
            $data = $class->$method();
        } else {
            $data = $class->$method($options);
        }

        return $data;
    }

}
