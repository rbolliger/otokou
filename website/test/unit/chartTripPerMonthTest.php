<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';



$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(64, new lime_output_color());


$scenarios = $ut->getBaseScenarios();


$options = 'month';


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
                0 => array(-88,111,333,-406,20,330),
            );
            break;

        case 3:

            $y = array(
                0 => array(0,100,100,-250,20,330),
                1 => array(-312,111,333,0,0,0),
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
        '2011-Jan',
        '2011-Feb',
        '2011-Mar',
        '2011-Apr',
        '2011-May',
        '2011-Jun',
    );

    return $x;
}
