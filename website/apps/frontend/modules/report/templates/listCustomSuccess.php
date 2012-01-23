<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>

<?php slot('content_title') ?>
<h1>Custom reports</h1>
<?php end_slot(); ?>

<?php if (count($custom)) : ?>

    <table class="reports_list"><tbody>
            <?php foreach ($custom as $report) : ?>
                <?php include_partial('report/report_properties', array('report' => $report)); ?>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php else: ?>

    <?php echo include_partial('report/no_reports'); ?>

<?php endif; ?>



