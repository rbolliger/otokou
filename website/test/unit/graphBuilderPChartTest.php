<?php

include dirname(__FILE__) . '/../bootstrap/Doctrine.php';


$app_configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
sfContext::createInstance($app_configuration);

$ut = new otokouTestFunctional(new sfBrowser());

require_once(realpath(dirname(__FILE__).'/../../lib/vendor/symfony/lib/helper/TagHelper.php'));
require_once(realpath(dirname(__FILE__).'/../../lib/vendor/symfony/lib/helper/AssetHelper.php'));


$t = new lime_test(6, new lime_output_color());


// ->generate()
$t->diag('->generate()');
$g = newGraph(array('user_id' => $ut->getUserId('user_gb_noCars')));
$t->cmp_ok($g->generate(), '===', false, '->generate() If the User has no cars, the graph cannot be generated.');

$g = newGraph(array('user_id' => $ut->getUserId('user_gb_noCharges')));
$t->cmp_ok($g->generate(), '===', false, '->generate() If the User has no charges, the graph cannot be generated.');

$g = newGraph();
$t->cmp_ok($g->generate(), '===', true, '->generate() If the User has cars, a chart is generated.');

// ->display()
$t->diag('->display()');
$g = newGraph(array('user_id' => $ut->getUserId('user_gb_noCars')));
$t->like($g->display(), '/Not enough data/', '->display() If the User has no cars, a message id displayed instead of the graph.');

$g = newGraph(array('user_id' => $ut->getUserId('user_gb_noCharges')));
$t->like($g->display(), '/Not enough data/', '->display() If the User has no charges, a message id displayed instead of the graph.');

$g = newGraph();
$t->like($g->display(), '/<img/', '->display() Even if the User has cars, the chart is displayed in an <img> tag.');


function getData($data = array()) {

    $fields = Doctrine_Core::getTable('Graph')->getFieldNames();

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

function newGraph($data = array(),$options = array(),$attributes = array()) {

    $ut = new otokouTestFunctional(new sfBrowser());

    $data = array_merge(
                    array(
                        'user_id'       => $ut->getUserId('user_gb'),
                        'graph_name'    => 'cost_per_km',
                        'range_type'    => 'distance',
                    ),
                    $data);

    return new GraphBuilderPChart(getData($data),$options,$attributes);
}
