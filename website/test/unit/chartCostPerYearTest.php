<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';



$ut = new chartSourceUtilityTest();

$t = new lime_test(144, new lime_output_color());


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



foreach ($scenarios as $key => $scenario) {
//    for ($key = 0; $key < 1; $key++) {
//        $scenario = $scenarios[$key];

    $x = getXForScenario($ut, $scenario);
    $y = getYForScenario($ut, $scenario);

    $ut->runTest($t, $scenario, 'buildCostPerYearChartData', $x, $y);
}

function getYForScenario($ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];

    switch ($case) {
        case 1:

            $y = array(
                0 => array(1036),
            );
            break;
        case 2:


            $y = array(
                0 => array(976),
                1 => array(60),
            );

            break;

        case 3:


            $y = array(
                0 => array(87),
                1 => array(949),
            );

            break;

        case 4:


            $y = array(
                0 => array(57),
                1 => array(30),
                2 => array(919),
                3 => array(30),
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
