<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>


<h1>Overall performances</h1>

<p>The values presented below are calculated overall the entire life period of the vehicle(s).</p>

<?php include_partial('charts/vehicles_performances',array('vehicles' => $report->getVehicles())); ?>

<h1>Costs</h1>

<h2>Cost per kilometer</h2>

<p>The cost per kilometer is calculated by considering the charges registered over the entire life of the vehicle(s).</p>

<?php echo $sf_data->getRaw('cost_per_km'); ?>


<h2>Annual cost</h2>

<p>The annual cost is calculated by considering the charges registered during the range (date and/or distance) specified for this report.</p>

<?php echo $sf_data->getRaw('cost_annual'); ?>


<h2>Costs allocation</h2>

<p>The cost allocation is calculated by considering the charges registered during the range (date and/or distance) specified for this report.</p>

<?php $d =  $sf_data->getRaw('cost_allocation'); echo $d->display(); ?>


<h1>Travel</h1>

<h2>Annual travel</h2>

<p>The annual travel is calculated by considering the charges registered during the range (date and/or distance) specified for this report.</p>

<?php  $d = $sf_data->getRaw('travel_annual'); echo $d->display();?>

<h2>Monthly travel</h2>

<p>The monthly travel is calculated by considering the charges registered during the range (date and/or distance) specified for this report.</p>

<?php echo $sf_data->getRaw('travel_monthly'); ?>

<h1>Fuel consumption</h1>

<p>The fuel consumption is calculated by considering the charges registered over the entire life of the vehicle(s).</p>

<?php echo $sf_data->getRaw('consumption_fuel'); ?>