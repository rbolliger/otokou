<table class="reports_list">
    <?php foreach ($category->getOwnReports(sfConfig::get('app_report_max_on_index')) as $i => $report): ?>
    <?php include_partial('report/report_properties', array('report' => $report)); ?>
    <?php endforeach; ?>
</table>
