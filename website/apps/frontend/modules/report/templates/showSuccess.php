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