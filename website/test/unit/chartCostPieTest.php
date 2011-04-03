<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';



$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(64, new lime_output_color());


$scenarios = array(
    array('stacked', 'stacked', 'distance'),
    array('stacked', 'stacked', 'date'),
    array('single', 'stacked', 'distance'),
    array('single', 'stacked', 'date'),
    array('single', 'single', 'distance'),
    array('single', 'single', 'date'),
    array('stacked', 'single', 'distance'),
    array('stacked', 'single', 'date'),
);

$options = array(
    'categories' => getCategories(),
    'vehicles' => getVehicles($ut->getUserId('user_gs')),
    'user_id' => $ut->getUserId('user_gs'),
);

foreach ($scenarios as $key => $scenario) {

    $y = getYForScenario($ut, $scenario);
    $x = getXForScenario($ut, $scenario);

    $fname = 'buildCostPieChartData';

    $options = array_merge($options,array('vehicle_display' => $scenario[0]));

    $g = $ut->runTest($t, $scenario, $fname, $x, $y, $options);
}

function getYForScenario($ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];


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

            $y = array(
                0 => array(60,0,0,976,0,0,0),
            );

            break;

        case 4:

            $y = array(
                0 => array(30,0,0,57,0,0,0),
                1 => array(30,0,0,919,0,0,0),
            );

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