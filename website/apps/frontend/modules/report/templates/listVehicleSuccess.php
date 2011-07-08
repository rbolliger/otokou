<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>


<?php if ($pager->getNbResults()) : ?>

    <h1>Available Reports for vehicle "<?php echo $vehicle ?>"</h1>
    
    <table class="reports_list">
        <tbody>
        <?php foreach ($pager->getResults() as $report) : ?>
        <?php include_partial('report/report_properties', array('report' => $report)); ?>
        <?php endforeach; ?>
        </tbody>
    </table>   
    
    <?php include_partial('report/pagination', array('pager' => $pager, 'url' => '@reports_list_vehicle?slug='.$vehicle->getSlug())); ?>
<?php else : ?>

                No reports available.<br>

<?php echo link_to('Create', '@report_new') ?>  a new custom report.

<?php endif; ?>



