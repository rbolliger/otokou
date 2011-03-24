<?php

$menu = new ioMenu('graphs_menu');
$menu->addChild('Costs');
$menu->addChild('Fuel consumption');

$menu['Costs']->addChild('Cost per km','@graph_cost_per_km');
$menu['Costs']->addChild('Cost per year','');
$menu['Costs']->addChild('Cost per month','');

$menu['Fuel consumption']->addChild('Litres per 100 km','');
$menu['Fuel consumption']->addChild('Litres per month','');

echo $menu->render();
?>
