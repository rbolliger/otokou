<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/chartSourceUtilityTest.class.php';


$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(400, new lime_output_color());


$scenarios = $ut->getBaseScenarios();

$params = array(
    'full_history' => false, // this is to define if data must be recovered from the beginning (by ignoring start_limit) or not
);

$file = dirname(__FILE__) . '/results/chartCostPerYearResults.yml';

//foreach ($scenarios as $key => $scenario) {
for ($index = 0; $index < 16; $index++) {
    $scenario = $scenarios[$index];

    $x = getXForScenario($ut, $scenario, $file);
    $y = getYForScenario($ut, $scenario, $file);

    $options = $scenario[2];

    $ut->runTest($t, $scenario, 'buildCostPerYearChartData', $x, $y, $options, $params);
}

function getYForScenario($ut, $scenario, $file) {

    $yaml = sfYaml::load($file);
    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];
    $limit = isset($scenario[3]) ? 'bounded' : 'unbounded';

    $y = $yaml['y'][$case][$limit][$range];


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

