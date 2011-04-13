<?php

class chartSourceUtilityTest extends otokouTestFunctional {

    public function getBaseScenarios() {

        return array(
            array('stacked', 'stacked', 'distance'),
            array('stacked', 'stacked', 'date'),
            array('single', 'stacked', 'distance'),
            array('single', 'stacked', 'date'),
            array('single', 'single', 'distance'),
            array('single', 'single', 'date'),
            array('stacked', 'single', 'distance'),
            array('stacked', 'single', 'date'),
        );
    }

    public function getChartSource($scenario, $categories_names = array(), $params = array()) {

        $vd = $scenario[0];
        $cd = $scenario[1];
        $scn = $this->getCase($vd, $cd);

        $defalut_categories = array('Fuel', 'Tax');
        $default_vehicles = array('car_gs_1', 'car_gs_2');

        switch ($scn) {
            case 1:

                $q = Doctrine_Core::getTable('Charge')->createQuery('c')
                                ->select('c.*')
                                ->leftJoin('c.User u')
                                ->andWhere('u.Username = ?', array('user_gs'));

                if ($categories_names) {
                    $q->leftJoin('c.Category ct')
                            ->andWhereIn('ct.name', $categories_names);
                }

                $history = $this->handleLimits($q, $scenario, $params);

                $q = $q->execute();

                $v = Doctrine_Core::getTable('Vehicle')->findByUserId($this->getUserId('user_gs'));
                $vid = $this->extractIds($v);

                $cid = $this->getCategoriesIdByName($categories_names);

                $series = array(new ChartDataSerie(array(
                        'raw_data' => $q,
                        'label' => 'all stacked',
                        'id' => 'allstacked',
                        'vehicle_id' => $vid,
                        'category_id' => $cid,
                    )));
                break;

            case 2:

                $cat = $categories_names ? $categories_names : $defalut_categories;


                $v = Doctrine_Core::getTable('Vehicle')->findByUserId($this->getUserId('user_gs'));
                $vid = $this->extractIds($v);

                $series = array();
                foreach ($cat as $key => $name) {

                    $q = Doctrine_Core::getTable('Charge')->createQuery('c')
                                    ->select('c.*')
                                    ->leftJoin('c.Category ct')
                                    ->andWhere('ct.Name = ?', $name)
                                    ->leftJoin('c.User u')
                                    ->andWhere('u.Username = ?', array('user_gs'));

                    $history = $this->handleLimits($q, $scenario, $params);

                    $q = $q->execute();

                    $cid = $this->getCategoriesIdByName($name);

                    $series[$key] = new ChartDataSerie(array(
                                'raw_data' => $q,
                                'label' => 'tax',
                                'id' => 'tax',
                                'vehicle_id' => $vid,
                                'category_id' => $cid,
                            ));
                }

                break;

            case 3:

                $cat = $categories_names ? $categories_names : array();

                $cid = $this->getCategoriesIdByName($cat);

                $series = array();
                foreach ($default_vehicles as $key => $name) {

                    $q = Doctrine_Core::getTable('Charge')->createQuery('c')
                                    ->select('c.*')
                                    ->leftJoin('c.Vehicle v')
                                    ->andWhere('v.Name = ?', $name)
                                    ->leftJoin('c.User u')
                                    ->andWhere('u.Username = ?', array('user_gs'));

                    if ($categories_names) {
                        $q->leftJoin('c.Category ct')
                                ->andWhereIn('ct.name', $categories_names);
                    }

                    $history = $this->handleLimits($q, $scenario, $params);

                    $q = $q->execute();

                    $v = Doctrine_Core::getTable('Vehicle')->findOneByName($name);
                    $vid = $this->extractIds($v);


                    $series[$key] = new ChartDataSerie(array(
                                'raw_data' => $q,
                                'label' => 'car_gs_1',
                                'id' => 'car_gs_1',
                                'vehicle_id' => $vid,
                                'category_id' => $cid,
                            ));
                }

                break;

            case 4:

                $cat = $categories_names ? $categories_names : $defalut_categories;

                $series = array();
                foreach ($default_vehicles as $vk => $vname) {

                    foreach ($cat as $ck => $cname) {

                        $q = Doctrine_Core::getTable('Charge')->createQuery('c')
                                        ->select('c.*')
                                        ->leftJoin('c.Vehicle v')
                                        ->andWhere('v.Name = ?', $vname)
                                        ->leftJoin('c.Category ct')
                                        ->andWhere('ct.Name = ?', $cname)
                                        ->leftJoin('c.User u')
                                        ->andWhere('u.Username = ?', array('user_gs'));

                        $history = $this->handleLimits($q, $scenario, $params);

                        $q = $q->execute();

                        $v = Doctrine_Core::getTable('Vehicle')->findOneByName($vname);
                        $vid = $this->extractIds($v);

                        $c = Doctrine_Core::getTable('Category')->findOneByName($cname);
                        $cid = $this->extractIds($c);

                        $series[] = new ChartDataSerie(array(
                                    'raw_data' => $q,
                                    'label' => 'car_gs_1_tax',
                                    'id' => 'car_gs_1_tax',
                                    'vehicle_id' => $vid,
                                    'category_id' => $cid,
                                ));
                    }
                }

                break;

            default:
                throw new sfException('Case not defined');
                break;
        }


        $cat = $categories_names ? $categories_names : $defalut_categories;
        $cid = $this->getCategoriesIdByName($cat);

        $params = array(
            'vehicle_display' => $vd,
            'category_display' => $cd,
            'categories_list' => $cid,
            'full_history' => $history,
        );

        if (isset($scenario[3])) {
            if ('distance' == $scenario[2]) {
                $params['kilometers_from'] = $scenario[3];
                $params['kilometers_to'] = $scenario[4];
            } else {
                $params['date_from'] = $scenario[3];
                $params['date_to'] = $scenario[4];
            }
        }

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

    public function runTest($t, $scenario, $fname, $x, $y, $options = array(), $params = array()) {


        $t->diag(sprintf('->%s() scenario (%s)', $fname, implode(', ', $scenario)));

        $cn = isset($params['categories_names']) ? $params['categories_names'] : array();

        $g = $this->getChartSource($scenario, $cn, $params);

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

        $t->cmp_ok(array_values($data['x']['values']), '==', $x, sprintf('->%s() "x-values" are ok', $fname));

        $t->ok(isset($data['y']), sprintf('->%s() returns a "y" field', $fname));
        $t->ok(isset($data['y']['series']), sprintf('->%s() returns a series array for "y" field', $fname));
        $t->ok(isset($data['y']['description']), sprintf('->%s() returns a description for "x" field', $fname));

        $t->cmp_ok(count($data['y']['series']), '===', count($y), sprintf('->%s() "y-values" series count ok', $fname));

        foreach ($data['y']['series'] as $key => $serie) {

            $t->ok(isset($serie['id']), sprintf('->%s() serie "%d" has an "id"', $fname, $key));
            $t->ok(isset($serie['label']), sprintf('->%s() serie "%d" has a "label"', $fname, $key));
            $t->ok(isset($serie['values']), sprintf('->%s() serie "%d" has some "values"', $fname, $key));

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

    private function getCategoriesIdByName($names) {

        if (!$names) {
            $c = Doctrine_Core::getTable('Category')->findAll();
        } else {
            $c = Doctrine_Core::getTable('Category')
                            ->createQuery('c')
                            ->whereIn('c.name', $names)
                            ->execute();
        }
        $cid = $this->extractIds($c);

        return $cid;
    }

    protected function handleLimits(Doctrine_Query $q, array $scenario, array $params) {

        // if no limits are set, we return the query
        if (count($scenario) < 4) {
            return $q;
        }

        $field = $scenario[2] == 'date' ? 'date' : 'kilometers';

        $history = $params['full_history'];
        if (!$history) {
            $q->addWhere($q->getRootAlias() . '.' . $field . ' >= ?', $scenario[3]);
        }

        $q->addWhere($q->getRootAlias() . '.' . $field . ' <= ?', $scenario[4]);

        return $history;
    }

}
