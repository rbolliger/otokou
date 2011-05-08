<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/chartSourceUtilityTest.class.php';


$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(128, new lime_output_color());


$scenarios = $ut->getBaseScenarios();

$options = array(
    'categories' => getCategories(),
    'vehicles' => getVehicles($ut->getUserId('user_gs')),
    'user_id' => $ut->getUserId('user_gs'),
);

$params = array(
    'full_history' => false, // this is to define if data must be recovered from the beginning (by ignoring start_limit) or not
);

//foreach ($scenarios as $key => $scenario) {
for ($index = 0; $index < 16; $index++) {
    $scenario = $scenarios[$index];

    $y = getYForScenario($ut, $scenario);
    $x = getXForScenario($ut, $scenario);

    $fname = 'buildCostPieChartData';

    $options = array_merge($options, array('vehicle_display' => $scenario[0]));

    $g = $ut->runTest($t, $scenario, $fname, $x, $y, $options, $params);
}

function getYForScenario($ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];
    $limit = isset($scenario[3]) ? true : false;

    // categories
//    [0] => Fuel
//    [1] => Initial investment
//    [2] => Leasing
//    [3] => Tax
//    [4] => Accessory
//    [5] => Insurance
//    [6] => Fine


    switch ($case) {
        case 1:
        case 3:

            $y = false;

            break;
        case 2:

            if (!$limit) {
                $y = array(
                    0 => array(60, 0, 0, 976, 0, 0, 0),
                );
            } else {
                if ('distance' == $range) {
                    $y = array(
                        0 => array(55, 0, 0, 976, 0, 0, 0),
                    );
                } else {
                    $y = array(
                        0 => array(60, 0, 0, 969, 0, 0, 0),
                    );
                }
            }

            break;

        case 4:

            if (!$limit) {
                $y = array(
                    0 => array(30, 0, 0, 57, 0, 0, 0),
                    1 => array(30, 0, 0, 919, 0, 0, 0),
                );
            } else {
                if ('distance' == $range) {
                    $y = array(
                        0 => array(30, 0, 0, 57, 0, 0, 0),
                        1 => array(25, 0, 0, 919, 0, 0, 0),
                    );
                } else {
                    $y = array(
                        0 => array(25, 0, 0, 57, 0, 0, 0),
                        1 => array(30, 0, 0, 919, 0, 0, 0),
                    );
                }
            }

            break;

        default:
            throw new sfException(sprintf('Unknown case %d', $case));
            break;
    }


    return $y;
}

function getXForScenario($ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);

    switch ($case) {
        case 1:
        case 3:
            $x = false;
            break;

        case 2:
        case 4:
            $cat = Doctrine_Core::getTable('Category')->findAll(Doctrine_Core::HYDRATE_ARRAY);

            $x = array();
            foreach ($cat as $c) {
                $x[] = $c['name'];
            }

            break;
    }

    return $x;
}

function getCategories() {


    $category_objects = Doctrine_Core::getTable('Category')->findAll(Doctrine_Core::HYDRATE_ARRAY);


    $categories = array();
    $names = array();

    foreach ($category_objects as $key => $values) {
        $categories[] = $values['id'];
        $names[] = $values['name'];
    }
    $nb_categories = count($categories);

    return array(
        'list' => $categories,
        'count' => $nb_categories,
        'names' => $names,
    );
}

function getVehicles($user_id) {


    $q = Doctrine_Core::getTable('Vehicle')->getVehiclesByUserIdQuery($user_id);

    $vehicle_objects = $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    foreach ($vehicle_objects as $key => $values) {
        $vehicles[] = $values['id'];
    }
    $nb_vehicles = count($vehicles);


    return array(
        'list' => $vehicles,
        'count' => $nb_vehicles,
    );
}