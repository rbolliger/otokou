<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>


<?php if (count($vehicle)) : ?>

    <h1>Available Reports for vehicle "<?php echo $vehicle ?>"</h1>

    <table class="reports_list">
        <tbody>
        <?php foreach ($vehicle->getOwnReports(1000) as $report) : ?>
        <?php include_partial('report/report_properties', array('report' => $report)); ?>
        <?php endforeach; ?>
        </tbody>
    </table>    
<?php else : ?>

                No reports available.<br>

<?php echo link_to('Create', '@report_new') ?>  a new custom report.

<?php endif; ?>



