<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';



$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(128, new lime_output_color());


$scenarios = $ut->getBaseScenarios();


$options = 'year';

$params = array(
    'full_history' => false, // this is to define if data must be recovered from the beginning (by ignoring start_limit) or not
);

//foreach ($scenarios as $key => $scenario) {
for ($index = 0; $index < 16; $index++) {
    $scenario = $scenarios[$index];

    $x = getXForScenario($ut, $scenario);
    $y = getYForScenario($ut, $scenario);

    $ut->runTest($t, $scenario, 'buildTripChartData', $x, $y, $options, $params);
}

function getYForScenario($ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];
    $limit = isset($scenario[3]) ? true : false;

    switch ($case) {
        case 2:
        case 4:
            $y = false;
            break;

        case 1:

            $y = array(
                0 => array(300),
            );

            if ($limit && 'date' == $range) {
                $y = array(
                    0 => array(76),
                );
            }

            break;

        case 3:

            $y = array(
                0 => array(300),
                1 => array(132),
            );

            if ($limit && 'date' == $range) {
                $y[0] = array(-50);
            }

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
