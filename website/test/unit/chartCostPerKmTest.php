<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';



$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(288, new lime_output_color());

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
    'full_history' => true, // this is to define if data must be recovered from the beginning (by ignoring start_limit) or not
);

foreach ($scenarios as $key => $scenario) {
//    for ($index = 9; $index < 10; $index++) {
//$scenario = $scenarios[$index];

    $options = $scenario[2];

    $y = getYForScenario($ut, $scenario);
    $x = getXForScenario($ut, $scenario);

    $fname = 'buildCostPerKmChartData';

    $g = $ut->runTest($t, $scenario, $fname, $x, $y, $options, $params);
}

function getYForScenario($ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];

    if ('distance' == $range) {
        $x = array(12, 50, 65, 70, 100, 123, 200, 300, 324, 400, 456, 654);
    } else {
        $x = array(100, 324, 12, 200, 65, 123, 300, 654, 456, 50, 70, 400);
    }

    switch ($case) {
        case 1:

            if ('distance' == $range) {
                $y = array(
                    0 => array(5, 55, 144, 148, 153, 164, 175, 189, 254, 257, 271, 1036),
                );
            } else {
                $y = array(
                    0 => array(5, 70, 75, 86, 175, 186, 200, 965, 979, 1029, 1033, 1036),
                );
            }

            break;
        case 2:

            if ('distance' == $range) {
                $y = array(
                    0 => array(5, 5, 5, 5, 10, 21, 32, 46, 46, 46, 60, 60),
                    1 => array(0, 50, 139, 143, 143, 143, 143, 143, 208, 211, 211, 976),
                );
            } else {
                $y = array(
                    0 => array(5, 5, 10, 21, 21, 32, 46, 46, 60, 60, 60, 60),
                    1 => array(0, 65, 65, 65, 154, 154, 154, 919, 919, 969, 973, 976),
                );
            }
            break;

        case 3:

            if ('distance' == $range) {
                $y = array(
                    0 => array(0, 50, 50, 54, 59, 59, 70, 84, 84, 87, 87, 87),
                    1 => array(5, 5, 94, 94, 94, 105, 105, 105, 170, 170, 184, 949),
                );
            } else {
                $y = array(
                    0 => array(5, 5, 5, 16, 16, 16, 30, 30, 30, 80, 84, 87),
                    1 => array(0, 65, 70, 70, 159, 170, 170, 935, 949, 949, 949, 949),
                );
            }
            break;

        case 4:

            if ('distance' == $range) {
                $y = array(
                    0 => array(0, 0, 0, 0, 5, 5, 16, 30, 30, 30, 30, 30),
                    1 => array(0, 50, 50, 54, 54, 54, 54, 54, 54, 57, 57, 57),
                    2 => array(5, 5, 5, 5, 5, 16, 16, 16, 16, 16, 30, 30),
                    3 => array(0, 0, 89, 89, 89, 89, 89, 89, 154, 154, 154, 919),
                );
            } else {
                $y = array(
                    0 => array(5, 5, 5, 16, 16, 16, 30, 30, 30, 30, 30, 30),
                    1 => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 50, 54, 57),
                    2 => array(0, 0, 5, 5, 5, 16, 16, 16, 30, 30, 30, 30),
                    3 => array(0, 65, 65, 65, 154, 154, 154, 919, 919, 919, 919, 919),
                );
            }
            break;

        default:
            throw new sfException(sprintf('Unknown case %d', $case));
            break;
    }

    foreach ($y as $ykey => $serie) {
        foreach ($serie as $skey => $value) {
            $y[$ykey][$skey] = $value / $x[$skey];
        }
    }

    $limit = isset($scenario[3]) ? true : false;
    if ($limit) {
        // we remove first and last value
        foreach ($y as $key => $serie) {
            $y[$key] = array_slice($serie, 1, count($serie) - 2);
        }
    }

    return $y;
}

function getXForScenario($ut, $scenario) {

    $range = $scenario[2];

    if ($range == 'distance') {
        $x = array(
            12,
            50,
            65,
            70,
            100,
            123,
            200,
            300,
            324,
            400,
            456,
            654
        );
    } else {
        $x = array(
            1293836400,
            1294095600,
            1294614000,
            1296514800,
            1296946800,
            1297724400,
            1298934000,
            1299625200,
            1300489200,
            1301608800,
            1304200800,
            1306879200,
        );
    }

    $limit = isset($scenario[3]) ? true : false;
    if ($limit) {
        // we remove first and last value
        $x = array_slice($x, 1, count($x) - 2);
    }

    return $x;
}

