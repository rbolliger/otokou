

<h1>Costs</h1>
<?php include_partial('report/chart', array('chart' => $charts['cost_per_km'])); ?>

<?php include_partial('report/chart', array('chart' => $charts['cost_annual'])); ?>

<?php include_partial('report/chart', array('chart' => $charts['cost_allocation'])); ?>

<h1>Travel</h1>

<?php include_partial('report/chart', array('chart' => $charts['travel_annual'])); ?>

<?php include_partial('report/chart', array('chart' => $charts['travel_monthly'])); ?>

<h1>Fuel consumption</h1>

<?php include_partial('report/chart', array('chart' => $charts['consumption_fuel'])); ?>