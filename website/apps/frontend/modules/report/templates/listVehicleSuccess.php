<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>


<?php if (count($reports)) : ?>

    <h1>Available Reports</h1>

<?php include_partial('reports_list', array('reports' => $reports)); ?>
    
<?php else : ?>

        No reports available <br>

<?php echo link_to('Create', '@report_new') ?>  a new custom report.

<?php endif; ?>



