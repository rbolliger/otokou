<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>

<?php if (count($vehicles) || count($custom)) : ?>

<h1>Available reports</h1>

<?php include_partial('reports_list_vehicle', array('categories' => $vehicles)) ?>

<?php include_partial('reports_list_custom', array('custom' => $custom)) ?>

<?php else : ?>

        No reports available. <br>

<?php endif; ?>



