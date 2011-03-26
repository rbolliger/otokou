<?php

$menu = new ioMenu('graphs_menu');
$menu->addChild('Costs');
$menu->addChild('Travel');
$menu->addChild('Fuel consumption');

$menu['Costs']->addChild('Cost per km','@graph_cost_per_km');
$menu['Costs']->addChild('Cost per year','@graph_cost_per_year');
$menu['Costs']->addChild('Cost allocation','@graph_cost_allocation');

$menu['Travel']->addChild('Annual travel','');
$menu['Travel']->addChild('Monthly travel','');

$menu['Fuel consumption']->addChild('Litres per 100 km','');
$menu['Fuel consumption']->addChild('Litres per month','');

echo $menu->render();
?>
