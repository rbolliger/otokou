<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';



$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(120, new lime_output_color());


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

$params = array(
    'categories_names' => array('Fuel'),
);

foreach ($scenarios as $key => $scenario) {

    $options = $scenario[2];

    $y = getYForScenario($ut, $scenario);
    $x = getXForScenario($ut, $scenario);

    $fname = 'buildConsumptionPerDistanceChartData';

    $g = $ut->runTest($t, $scenario, $fname, $x, $y, $options,$params);
}

function getYForScenario($ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];

    if ('distance' == $range) {
        $x = array(12, 100, 123, 200, 300, 456);
    } else {
        $x = array(100, 12, 200, 123, 300, 456);
    }

    switch ($case) {
        case 1:
        case 2:

            if ('distance' == $range) {
                $y = array(
                    0 => array(254, 274, 397, 417, 437, 1091),
                );
            } else {
                $y = array(
                    0 => array(20, 274, 294, 417, 437, 1091),
                );
            }

            break;

        case 3:
        case 4:

            if ('distance' == $range) {
                $y = array(
                    0 => array(0, 20, 20, 40, 60, 60),
                    1 => array(254, 254, 377, 377, 377, 1031),
                );
            } else {
                $y = array(
                    0 => array(20, 20, 40, 40, 60, 60),
                    1 => array(0, 254, 254, 377, 377, 1031),
                );
            }
            break;

        default:
            throw new sfException(sprintf('Unknown case %d', $case));
            break;
    }

    foreach ($y as $ykey => $serie) {
        foreach ($serie as $skey => $value) {
            $dist = $x[$skey];
            if ($dist == 0){
                $dist = 0.01;
            }
            
            $y[$ykey][$skey] = $value / $dist * 100;
        }
    }

    return $y;
}

function getXForScenario($ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];

    if ('distance' == $range) {
        $x = array(12, 100, 123, 200, 300, 456);
    } else {
        $x = array(1293836400, 1294614000, 1296514800, 1297724400, 1298934000, 1300489200);
    }
    return $x;
}

