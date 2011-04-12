<?php

slot('leftcol');
include_partial('charts_menu');
end_slot();
?>

<?php

slot('rightcol');
include_partial('filters', array('filters' => $filters));
end_slot();
?>


<h1>Vehicles Performances</h1>

<?php include_partial('vehicles_performances',array('vehicles' => $vehicles)); ?>

<?php include_partial('debug',array('debug' => $debug)) ?>


