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

                <tfoot>
                    <tr>
                        <th colspan="6">
                            <?php if ($pager->haveToPaginate()): ?>
                                <?php include_partial('report/pagination', array('pager' => $pager, 'url' => '@reports_list_vehicle?slug=' . $vehicle->getSlug())) ?>
                            <?php endif; ?>

                            <?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults(), 'sf_admin') ?>
                            <?php if ($pager->haveToPaginate()): ?>
                                <?php echo __('(page %%page%%/%%nb_pages%%)', array('%%page%%' => $pager->getPage(), '%%nb_pages%%' => $pager->getLastPage()), 'sf_admin') ?>
                            <?php endif; ?>
                        </th>
                    </tr>
                </tfoot>

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



