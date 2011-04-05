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


$options = 'year';


foreach ($scenarios as $key => $scenario) {

    $x = getXForScenario($ut, $scenario);
    $y = getYForScenario($ut, $scenario);

    $ut->runTest($t, $scenario, 'buildTripChartData', $x, $y, $options);
}

function getYForScenario($ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];

    switch ($case) {
        case 2:
        case 4:
            $y = false;
            break;

        case 1:

            $y = array(
                0 => array(300),
            );
            break;

        case 3:

            $y = array(
                0 => array(300),
                1 => array(132),
            );

            break;


        default:
            throw new sfException(sprintf('Unknown case %d', $case));
            break;
    }

    return $y;
}

function getXForScenario($ut, $scenario) {

    $x = array(
        2011,
    );

    return $x;
}
