<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/chartSourceUtilityTest.class.php';


$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(272, new lime_output_color());

$file = dirname(__FILE__) . '/results/chartConsumptionPerDistanceResults.yml';

$scenarios = $ut->getBaseScenarios();

$params = array(
    'categories_names' => array('Fuel'),
    'full_history' => true, // this is to define if data must be recovered from the beginning (by ignoring start_limit) or not
);


for ($index = 0; $index < 16; $index++) {
    $scenario = $scenarios[$index];

    $options = $scenario[2];

    $y = getYForScenario($ut, $scenario, $file);
    $x = getXForScenario($ut, $scenario, $file);

    $fname = 'buildConsumptionPerDistanceChartData';

    $g = $ut->runTest($t, $scenario, $fname, $x, $y, $options, $params);
}

function getYForScenario($ut, $scenario, $file) {

    $yaml = sfYaml::load($file);
    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];
    $limit = isset($scenario[3]) ? 'bounded' : 'unbounded';

    $x = $yaml['y']['x'][$case][$limit][$range];
    $y = $yaml['y'][$case][$limit][$range];
//print_r($x);
//print_r($y);
    foreach ($y as $ykey => $serie) {

        $x_first = $yaml['y']['x'][$case]['first'][$range][$ykey];

        foreach ($serie as $skey => $value) {
            $dist = $x[$skey] - $x_first;
            if ($dist == 0) {
                $y[$ykey][$skey] = null;
            } else {
                $y[$ykey][$skey] = $value / $dist * 100;
            }
        }
    }

    return $y;
}

function getXForScenario($ut, $scenario, $file) {

    $yaml = sfYaml::load($file);
    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];
    $limit = isset($scenario[3]) ? 'bounded' : 'unbounded';

    $x = $yaml['x'][$case][$limit][$range];

    return $x;
}

