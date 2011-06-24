<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>


<?php if (count($custom)) : ?>

    <h2>List of available custom reports</h2>

    <table class="reports_list"><tbody>
        <?php foreach ($custom as $report) : ?>
        <?php include_partial('report/report_properties', array('report' => $report)); ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    

<?php endif; ?>



