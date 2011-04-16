<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';



$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(288, new lime_output_color());

$scenarios = $ut->getBaseScenarios();

$params = array(
    'full_history' => true, // this is to define if data must be recovered from the beginning (by ignoring start_limit) or not
);

$file = dirname(__FILE__) . '/results/chartCostPerKmResults.yml';

//foreach ($scenarios as $key => $scenario) {
for ($index = 0; $index < 16; $index++) {
    $scenario = $scenarios[$index];

    $options = $scenario[2];

    $y = getYForScenario($ut, $scenario, $file);
    $x = getXForScenario($ut, $scenario, $file);

    $fname = 'buildCostPerKmChartData';

    $g = $ut->runTest($t, $scenario, $fname, $x, $y, $options, $params);
}

function getYForScenario($ut, $scenario, $file) {

    $yaml = $ut->loadResultsFile($file, $scenario);
    $case = $yaml['case'];
    $range = $yaml['range'];
    $limit = $yaml['limit'];

    $x = $yaml['y']['x'][$case][$limit][$range];
    $y = $yaml['y'][$case][$limit][$range];


    foreach ($y as $ykey => $serie) {
        foreach ($serie as $skey => $value) {
            $y[$ykey][$skey] = $value / $x[$skey];
        }
    }

    return $y;
}

function getXForScenario($ut, $scenario, $file) {

    $yaml = $ut->loadResultsFile($file, $scenario);
    $case = $yaml['case'];
    $range = $yaml['range'];
    $limit = $yaml['limit'];

    $x = $yaml['x'][$case][$limit][$range];

    return $x;
}

