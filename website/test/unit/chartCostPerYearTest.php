<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';



$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(288, new lime_output_color());


$scenarios = array_merge($ut->getBaseScenarios(),
                array(
                    array('stacked', 'stacked', 'distance', 6, 500),
                    array('stacked', 'stacked', 'date', '2011-01-02', '2012-05-01'),
                    array('single', 'stacked', 'distance', 6, 500),
                    array('single', 'stacked', 'date', '2010-01-02', '2011-04-27'),
                    array('single', 'single', 'distance', 17, 800),
                    array('single', 'single', 'date', '2011-01-02', '2012-05-01'),
                    array('stacked', 'single', 'distance', 17, 800),
                    array('stacked', 'single', 'date', '2010-01-02', '2011-04-27'),
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

    $options = $scenario[2];

    $ut->runTest($t, $scenario, 'buildCostPerYearChartData', $x, $y, $options, $params);
}

function getYForScenario($ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];

    $limit = isset($scenario[3]) ? true : false;


    switch ($case) {
        case 1:

            if (!$limit) {
                $y = array(
                    0 => array(1036),
                );
            } else {

                if ('distance' == $range) {
                    $y = array(
                        0 => array(271),
                    );
                } else {
                    $y = array(
                        0 => array(1031),
                    );
                }
            }
            break;
        case 2:

            if (!$limit) {
                $y = array(
                    0 => array(60),
                    1 => array(976),
                );
            } else {
                if ('distance' == $range) {
                    $y = array(
                        0 => array(55),
                        1 => array(976),
                    );
                } else {
                    $y = array(
                        0 => array(60),
                        1 => array(969),
                    );
                }
            }

            break;

        case 3:

            if (!$limit) {
                $y = array(
                    0 => array(87),
                    1 => array(949),
                );
            } else {
                if ('distance' == $range) {
                    $y = array(
                        0 => array(87),
                        1 => array(184),
                    );
                } else {
                    $y = array(
                        0 => array(80),
                        1 => array(949),
                    );
                }
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
                if ('distance' == $range) {
                    $y = array(
                        0 => array(30),
                        1 => array(57),
                        2 => array(25),
                        3 => array(919),
                    );
                } else {
                    $y = array(
                        0 => array(25),
                        1 => array(57),
                        2 => array(30),
                        3 => array(919),
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

