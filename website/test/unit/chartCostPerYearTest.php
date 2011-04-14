<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';



$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(252, new lime_output_color());


$scenarios = array_merge($ut->getBaseScenarios(),
                array(
                    array('stacked', 'stacked', 'distance', 50, 456),
                    array('stacked', 'stacked', 'date', '2011-1-4', '2011-5-1'),
                    array('single', 'stacked', 'distance', 50, 456),
                    array('single', 'stacked', 'date', '2011-1-4', '2011-5-1'),
                    array('single', 'single', 'distance', 50, 456),
                    array('single', 'single', 'date', '2011-1-4', '2011-5-1'),
                    array('stacked', 'single', 'distance', 50, 456),
                    array('stacked', 'single', 'date', '2011-1-4', '2011-5-1'),
                )
);

$params = array(
    'full_history' => false, // this is to define if data must be recovered from the beginning (by ignoring start_limit) or not
);



//foreach ($scenarios as $key => $scenario) {
for ($index = 0; $index < 16; $index++) {
    $scenario = $scenarios[$index];

    $x = getXForScenario($ut, $scenario);
    $y = getYForScenario($ut, $scenario);

    $ut->runTest($t, $scenario, 'buildCostPerYearChartData', $x, $y, array(), $params);
}

function getYForScenario($ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];

    $limit = isset($scenario[3]) ? true : false;


    if (isDistanceAndLimits($scenario)) {
        return array();
    }

    switch ($case) {
        case 1:

            if (!$limit) {
                $y = array(
                    0 => array(1036),
                );
            } else {
                $y = array(
                    0 => array(1028),
                );
            }
            break;
        case 2:

            if (!$limit) {
                $y = array(
                    0 => array(60),
                    1 => array(976),
                );
            } else {
                $y = array(
                    0 => array(55),
                    1 => array(973),
                );
            }

            break;

        case 3:

            if (!$limit) {
                $y = array(
                    0 => array(87),
                    1 => array(949),
                );
            } else {
                $y = array(
                    0 => array(79),
                    1 => array(949),
                );
            }

            break;

        case 4:

            if (!$limit) {
                $y = array(
                    0 => array(30),
                    1 => array(57),
                    2 => array(30),
                    3 => array(919),
                );
            } else {
                $y = array(
                    0 => array(25),
                    1 => array(54),
                    2 => array(30),
                    3 => array(919),
                );
            }

            break;

        default:
            throw new sfException(sprintf('Unknown case %d', $case));
            break;
    }

    return $y;
}

function getXForScenario($ut, $scenario) {

    if (isDistanceAndLimits($scenario)) {
        return array();
    }

    $x = array(
        2011,
    );

    return $x;
}

function isDistanceAndLimits($scenario) {
    if ('distance' == $scenario[2] && isset($scenario[3])) {
        return true;
    }

    return false;
}

