<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';



$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(288, new lime_output_color());

$scenarios = $ut->getBaseScenarios();

$params = array(
    'full_history' => true, // this is to define if data must be recovered from the beginning (by ignoring start_limit) or not
);

//foreach ($scenarios as $key => $scenario) {
for ($index = 0; $index < 16; $index++) {
    $scenario = $scenarios[$index];

    $options = $scenario[2];

    $y = getYForScenario($ut, $scenario);
    $x = getXForScenario($ut, $scenario);

    $fname = 'buildCostPerKmChartData';

    $g = $ut->runTest($t, $scenario, $fname, $x, $y, $options, $params);
}

function getYForScenario($ut, $scenario) {


    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];
    $limit = isset($scenario[3]) ? 'bounded' : 'unbounded';

    $yaml = sfYaml::load(dirname(__FILE__) . '/results/chartCostPerKmResults.yml');

    $x = $yaml['y']['x'][$case][$limit][$range];
    $y = $yaml['y'][$case][$limit][$range];


    foreach ($y as $ykey => $serie) {
        foreach ($serie as $skey => $value) {
            $y[$ykey][$skey] = $value / $x[$skey];
        }
    }

//    $limit = isset($scenario[3]) ? true : false;
//    if ($limit) {
//        // we remove first and last value
//        foreach ($y as $key => $serie) {
//            $y[$key] = array_slice($serie, 1, count($serie) - 2);
//        }
//    }

    return $y;
}

function getXForScenario($ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];
    $limit = isset($scenario[3]) ? 'bounded' : 'unbounded';

    $yaml = sfYaml::load(dirname(__FILE__) . '/results/chartCostPerKmResults.yml');

    $x = $yaml['x'][$case][$limit][$range];

    return $x;
}

