<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/chartSourceUtilityTest.class.php';

$file = dirname(__FILE__) . '/results/chartTripPerYearResults.yml';

$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(144, new lime_output_color());


$scenarios = $ut->getBaseScenarios();


$params = array(
    'full_history' => false, // this is to define if data must be recovered from the beginning (by ignoring start_limit) or not
);

//foreach ($scenarios as $key => $scenario) {
for ($index = 0; $index < 16; $index++) {
    $scenario = $scenarios[$index];

    $x = getXForScenario($ut, $scenario, $file);
    $y = getYForScenario($ut, $scenario, $file);

    $options = array(
        'unit' => 'year',
        'range_type' => $scenario[2],
    );

    $ut->runTest($t, $scenario, 'buildTripChartData', $x, $y, $options, $params);
}

function getYForScenario($ut, $scenario, $file) {

    $yaml = sfYaml::load($file);
    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];
    $limit = isset($scenario[3]) ? 'bounded' : 'unbounded';

    if ($case == 2 || $case == 4) {
        return false;
    }

    $y = $yaml['y'][$case][$range][$limit];

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
