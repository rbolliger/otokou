<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>

<?php slot('content_title') ?>
<h1>Reports</h1>
<?php end_slot(); ?>

<?php $vehicles = $pager->getResults(); ?>

<?php if (count($vehicles)) : ?>

    <div id="sf_admin_container">
        <div id="report_container">

            <?php include_partial('reports_list_vehicle', array('categories' => $vehicles)) ?>

        </div>
    </div>  

    <?php if ($pager->haveToPaginate()): ?>
        <div class="pagination">
            <?php include_partial('report/pagination', array('pager' => $pager, 'url' => '@report_index')) ?>

            <?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults(), 'sf_admin') ?>

            <?php echo __('(page %%page%%/%%nb_pages%%)', array('%%page%%' => $pager->getPage(), '%%nb_pages%%' => $pager->getLastPage()), 'sf_admin') ?>
        </div>
    <?php endif; ?>


<?php else : ?>

    <?php echo include_partial('report/no_reports'); ?>
<?php endif; ?>



