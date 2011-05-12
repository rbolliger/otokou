<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';
include dirname(__FILE__) . '/../../lib/test/chartSourceUtilityTest.class.php';

$ut = new chartSourceUtilityTest(new sfBrowser());

$t = new lime_test(144, new lime_output_color());


$scenarios = $ut->getBaseScenarios();

$params = array(
    'full_history' => false, // this is to define if data must be recovered from the beginning (by ignoring start_limit) or not
);

//foreach ($scenarios as $key => $scenario) {
for ($index = 0; $index < 16; $index++) {
    $scenario = $scenarios[$index];

    $x = getXForScenario($ut, $scenario);
    $y = getYForScenario($x, $ut, $scenario);

    $options = array(
        'unit' => 'month',
        'range_type' => $scenario[2],
    );

    $ut->runTest($t, $scenario, 'buildTripChartData', $x, $y, $options, $params);
}

function getYForScenario($x, $ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];
    $limit = isset($scenario[3]) ? true : false;

    $y_base = array_fill(0, count($x), 0);

    switch ($case) {
        case 2:
        case 4:
            $y = false;
            break;

        case 1:

            if ('distance' == $range) {

                if (!$limit) {

                    $y_base[0] = 312;
                    $y_base[1] = -201;
                    $y_base[2] = 333;
                    $y_base[25] = -391;
                    $y_base[26] = 589;
                    $y_base[58] = -354;
                    $y_base[60] = 79700;
                    $y_base[61] = 3000;
                    $y_base[62] = -453;
                    $y_base[64] = -82477;
                    $y_base[65] = 330;
                } else {

                    $y_base [0] = 0;
                    $y_base[1] = -201;
                    $y_base[2] = 333;
                    $y_base[58] = -156;
                    $y_base[60] = -200;
                    $y_base[61] = 100;
                    $y_base[64] = -130;
                    $y_base[65] = 330;
                }
            } else {

                if (!$limit) {

                    $y_base [0] = -312;
                    $y_base[1] = 111;
                    $y_base[2] = 333;
                    $y_base[25] = -391;
                    $y_base[26] = 589;
                    $y_base[58] = -604;
                    $y_base[60] = 79950;
                    $y_base[61] = 3000;
                    $y_base[62] = -1500;
                    $y_base[64] = -81430;
                    $y_base[65] = 330;
                } else {
                    $y_base [0] = 0;
                    $y_base[1] = 111;
                    $y_base[2] = 333;
                    $y_base[25] = -391;
                    $y_base[26] = 589;
                    $y_base[58] = -604;
                    $y_base[60] = 79950;
                    $y_base[61] = 3000;
                    $y_base[62] = -1500;
                    $y_base[64] = -81430;
                    $y_base[65] = 330;
                }
            }

            $y = array(0 => $y_base);

            break;

        case 3:

            $y0 = $y_base;
            $y1 = $y_base;
            $y2 = $y_base;

            if ('distance' == $range) {

                if (!$limit) {

                    $y0[58] = 250;
                    $y0[60] = -200;
                    $y0[61] = 100;
                    $y0[64] = -130;
                    $y0[65] = 330;

                    $y1[0] = 312;
                    $y1[1] = -201;
                    $y1[2] = 333;
                    $y1[25] = -391;
                    $y1[26] = 589;

                    $y2[60] = 46;
                    $y2[61] = 3000;
                    $y2[62] = -453;

                    
                } else {

                    $y0[58] = 250;
                    $y0[60] = -200;
                    $y0[61] = 100;
                    $y0[64] = -130;
                    $y0[65] = 330;

                    $y1[0] = 312;
                    $y1[1] = -201;
                    $y1[2] = 333;
                    $y1[25] = -391;
                    $y1[26] = 589;

                    $y2[60] = 46;
                    $y2[61] = 1756;
                    $y2[62] = -256;
                }
            } else {

                if (!$limit) {

                    $y0[58] = -250;
                    $y0[60] = 50;
                    $y0[61] = 100;
                    $y0[64] = -130;
                    $y0[65] = 330;

                    $y1[0] = -312;
                    $y1[1] = 111;
                    $y1[2] = 333;
                    $y1[25] = -391;
                    $y1[26] = 589;

                    $y2[60] = 46;
                    $y2[61] = 3000;
                    $y2[62] = -1500;
                    
                } else {

                    $y0[32] = -250;
                    $y0[34] = 50;
                    $y0[35] = 100;

                    $y1[0] = 0;

                    $y2[34] = 46;
                    $y2[35] = 3000;
                    $y2[36] = -1500;

                }
            }

            $y = array(
                0 => $y0,
                1 => $y1,
                2 => $y2,
                );
            break;


        default:
            throw new sfException(sprintf('Unknown case %d', $case));
            break;
    }

    return $y;
}

function getXForScenario($ut, $scenario) {

    $case = $ut->getCase($scenario[0], $scenario[1]);
    $range = $scenario[2];
    $limit = isset($scenario[3]) ? true : false;

    $x = array(
        '2006-Jan', '2006-Feb', '2006-Mar', '2006-Apr', '2006-May', '2006-Jun', '2006-Jul', '2006-Aug', '2006-Sep', '2006-Oct', '2006-Nov', '2006-Dec',
        '2007-Jan', '2007-Feb', '2007-Mar', '2007-Apr', '2007-May', '2007-Jun', '2007-Jul', '2007-Aug', '2007-Sep', '2007-Oct', '2007-Nov', '2007-Dec',
        '2008-Jan', '2008-Feb', '2008-Mar', '2008-Apr', '2008-May', '2008-Jun', '2008-Jul', '2008-Aug', '2008-Sep', '2008-Oct', '2008-Nov', '2008-Dec',
        '2009-Jan', '2009-Feb', '2009-Mar', '2009-Apr', '2009-May', '2009-Jun', '2009-Jul', '2009-Aug', '2009-Sep', '2009-Oct', '2009-Nov', '2009-Dec',
        '2010-Jan', '2010-Feb', '2010-Mar', '2010-Apr', '2010-May', '2010-Jun', '2010-Jul', '2010-Aug', '2010-Sep', '2010-Oct', '2010-Nov', '2010-Dec',
        '2011-Jan', '2011-Feb', '2011-Mar', '2011-Apr', '2011-May', '2011-Jun'
    );

    if ($case == 3 && $limit && 'date' == $range) {
        $x = array_slice($x, 26, 37);
    }

    return $x;
}
