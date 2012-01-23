<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>

<?php slot('content_title') ?>
<h1>Reports</h1>
<?php end_slot(); ?>

<?php if (count($vehicles) || count($custom)) : ?>

    <?php include_partial('reports_list_vehicle', array('categories' => $vehicles)) ?>

    <?php include_partial('reports_list_custom', array('custom' => $custom)) ?>

<?php else : ?>

    <?php echo include_partial('report/no_reports'); ?>
<?php endif; ?>



