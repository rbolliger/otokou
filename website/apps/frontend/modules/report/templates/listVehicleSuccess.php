<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>

<?php slot('content_title') ?>
<h1>Reports for "<?php echo $vehicle ?>"</h1>
<?php end_slot(); ?>

<?php if ($pager->getNbResults()) : ?>

    <div id="sf_admin_container">
        <div id="report_container">

            <table class="reports_list">

                <?php echo include_partial('report/reports_list_thead'); ?>

                <?php echo include_partial('report/reports_list_tfoot',array('pager' => $pager, 'url' => '@reports_list_vehicle?slug=' . $vehicle->getSlug())); ?>

                <tbody>
                    <?php foreach ($pager->getResults() as $report) : ?>
                        <?php include_partial('report/report_properties', array('report' => $report)); ?>
                    <?php endforeach; ?>
                </tbody>
            </table>   
        </div>
    </div>  


<?php else : ?>

    <?php echo include_partial('report/no_reports'); ?>

<?php endif; ?>



