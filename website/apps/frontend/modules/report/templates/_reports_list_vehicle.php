
<?php foreach ($categories as $category): ?>


    <div class="reports_category" id="<?php echo 'category_'.$category->getSlug() ?>">
        <h3 class="report_category_title"><?php
    echo link_to(
            $category->getName(), '@reports_list_vehicle?slug=' . $category->getSlug()
    )
    ?></h3>


        <table class="reports_list">
            <thead>
                <tr>
                    <th>Is new?</th>
                    <th>Name</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Vehicles</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($category->getOwnReports(sfConfig::get('app_report_max_on_index')) as $i => $report): ?>
                    <?php include_partial('report/report_properties', array('report' => $report)); ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (($count = $category->countReports() - sfConfig::get('app_report_max_on_index')) > 0): ?>
            <div class="more_reports">
                and <?php echo link_to($count, '@reports_list_vehicle?slug=' . $category->getSlug()) ?>
                more...
            </div>
        <?php endif; ?>
    </div>

<?php endforeach; ?>


