<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>


<?php if (count($reports)) : ?>
<h1>New reports available</h1>
<?php include_partial('reports_list', array('reports' => $reports)) ?>

<?php else : ?>

        No new reports available. <br>

<?php endif; ?>



