<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';



$ut = new graphSourceUtilityTest();

$t = new lime_test(34, new lime_output_color());


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



foreach ($scenarios as $key => $scenario) {

    $t->diag(sprintf('->buildCostPerKmGraphData() scenario %d (%s)', $key, implode(', ', $scenario)));
    $g = $ut->getGraphSource($scenario[0], $scenario[1]);
    $data = $g->buildCostPerKmGraphData($scenario[2]);
    $x = $ut->getXForScenario($scenario);
    $y = getYForScenario($ut,$scenario);

    $t->cmp_ok(array_values($data['x']['values']), '==', $x, '->buildCostPerKmGraphData() x-values are ok');
    $t->cmp_ok(count($data['y']['series']), '===', count($y), '->buildCostPerKmGraphData() y-values series count ok');

    foreach ($data['y']['series'] as $ykey => $values) {
        $t->cmp_ok(array_values($data['y']['series'][$ykey]['values']), '==', $y[$ykey], sprintf('->buildCostPerKmGraphData() y-values for serie "%d" ok', $ykey));
    }
}

function getYForScenario($ut,$scenario) {

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
                    0 => array(0, 50, 139, 143, 143, 143, 143, 143, 208, 211, 211, 976),
                    1 => array(5, 5, 5, 5, 10, 21, 32, 46, 46, 46, 60, 60),
                );
            } else {
                $y = array(
                    0 => array(0, 65, 65, 65, 154, 154, 154, 919, 919, 969, 973, 976),
                    1 => array(5, 5, 10, 21, 21, 32, 46, 46, 60, 60, 60, 60),
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
                    0 => array(0, 50, 50, 54, 54, 54, 54, 54, 54, 57, 57, 57),
                    1 => array(0, 0, 0, 0, 5, 5, 16, 30, 30, 30, 30, 30),
                    2 => array(0, 0, 89, 89, 89, 89, 89, 89, 154, 154, 154, 919),
                    3 => array(5, 5, 5, 5, 5, 16, 16, 16, 16, 16, 30, 30),
                );
            } else {
                $y = array(
                    0 => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 50, 54, 57),
                    1 => array(5, 5, 5, 16, 16, 16, 30, 30, 30, 30, 30, 30),
                    2 => array(0, 65, 65, 65, 154, 154, 154, 919, 919, 919, 919, 919),
                    3 => array(0, 0, 5, 5, 5, 16, 16, 16, 30, 30, 30, 30),
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

    return $y;
}

