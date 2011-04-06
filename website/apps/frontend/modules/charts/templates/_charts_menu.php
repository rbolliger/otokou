<?php

$menu = new ioMenu('charts_menu');
$menu->addChild('Costs');
$menu->addChild('Travel');
$menu->addChild('Fuel consumption');

$menu['Costs']->addChild('Cost per km','@chart_cost_per_km');
$menu['Costs']->addChild('Cost per year','@chart_cost_per_year');
$menu['Costs']->addChild('Cost allocation','@chart_cost_allocation');

$menu['Travel']->addChild('Annual travel','@chart_trip_annual');
$menu['Travel']->addChild('Monthly travel','@chart_trip_monthly');

$menu['Fuel consumption']->addChild('Litres per 100 km','@chart_consumption_per_distance');
$menu['Fuel consumption']->addChild('Litres per month','');

echo $menu->render();
?>
