<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>


<h1>Overall performances</h1>

<p>The values presented below are calculated overall the entire life period of the vehicle(s).</p>

<?php include_partial('charts/vehicles_performances',array('vehicles' => $report->getVehicles())); ?>



<?php include_partial('report/charts',array('charts' => $charts)); ?>



