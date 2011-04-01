<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';


$app_configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
sfContext::createInstance($app_configuration);

$ut = new otokouTestFunctional(new sfBrowser());

require_once(realpath(dirname(__FILE__) . '/../../lib/vendor/symfony/lib/helper/TagHelper.php'));
require_once(realpath(dirname(__FILE__) . '/../../lib/vendor/symfony/lib/helper/AssetHelper.php'));


$t = new lime_test(7, new lime_output_color());


// ->generate()
$t->diag('->generate()');
$g = newChart(array('user_id' => $ut->getUserId('user_gb_noCars')));
$t->cmp_ok($g->generate(), '===', false, '->generate() If the User has no cars, the chart cannot be generated.');

$g = newChart(array('user_id' => $ut->getUserId('user_gb_noCharges')));
$t->cmp_ok($g->generate(), '===', false, '->generate() If the User has no charges, the chart cannot be generated.');

$g = newChart();
$t->cmp_ok($g->generate(), '===', true, '->generate() If the User has cars, a chart is generated.');

// ->buildCostPerKmChartData()
sfConfig::set('app_charts_force_generate', true);
$options = array(
    'user_id' => $ut->getUserId('user_gs'),
    'chart_name' => 'cost_per_km',
    'range_type' => 'distance',
    'vehicle_display' => 'stacked',
    'category_display' => 'single',
    'format' => 'png',
);
$g = newChart($options);
$t->cmp_ok($g->generate(), '===', true, '->generate() A cost_per_km chart is generated.');



// ->display()
$t->diag('->display()');
$g = newChart(array('user_id' => $ut->getUserId('user_gb_noCars')));
$t->like($g->display(), '/Not enough data/', '->display() If the User has no cars, a message id displayed instead of the chart.');

$g = newChart(array('user_id' => $ut->getUserId('user_gb_noCharges')));
$t->like($g->display(), '/Not enough data/', '->display() If the User has no charges, a message id displayed instead of the chart.');

$g = newChart();
$t->like($g->display(), '/<img/', '->display() Even if the User has cars, the chart is displayed in an <img> tag.');

function getData($data = array()) {

    $fields = Doctrine_Core::getTable('Chart')->getFieldNames();

    $defaults = array_combine($fields, array_fill(0, count($fields), null));

    unset(
            $defaults['created_at'],
            $defaults['updated_at'],
            $defaults['sha'],
            $defaults['id']
    );

    $foreign = array(
        'vehicles_list' => null,
        'categories_list' => null,
    );

    $defaults = array_merge($defaults, $foreign);

    return array_merge($defaults, $data);
}

function newChart($data = array(), $options = array(), $attributes = array()) {

    $ut = new otokouTestFunctional(new sfBrowser());

    $data = array_merge(
                    array(
                        'user_id' => $ut->getUserId('user_gb'),
                        'chart_name' => 'cost_per_km',
                        'range_type' => 'distance',
                        'vehicle_display' => 'stacked',
                        'category_display' => 'single',
                        'format' => 'png',
                    ),
                    $data);

    return new ChartBuilderPChart(getData($data), $options, $attributes);
}
