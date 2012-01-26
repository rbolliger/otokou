<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>

<?php slot('content_title') ?>
<h1>Reports</h1>
<?php end_slot(); ?>

<?php if (count($vehicles) || count($custom)) : ?>

    <div id="sf_admin_container">
        <div id="report_container">

            <?php include_partial('reports_list_vehicle', array('categories' => $vehicles)) ?>

            <?php include_partial('reports_list_custom', array('custom' => $custom)) ?>

        </div>
    </div>  

<?php else : ?>

    <?php echo include_partial('report/no_reports'); ?>
<?php endif; ?>



