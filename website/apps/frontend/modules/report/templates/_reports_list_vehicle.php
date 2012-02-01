
<?php foreach ($categories as $category): ?>


    <div class="reports_category" id="<?php echo 'category_' . $category->getSlug() ?>">
        <h3 class="report_category_title"><?php
    echo link_to(
            $category->getName(), '@reports_list_vehicle?slug=' . $category->getSlug()
    )
    ?></h3>


        <table class="reports_list">
            <?php echo include_partial('report/reports_list_thead'); ?>
            <?php if (($count = $category->countReports() - sfConfig::get('app_report_max_on_index')) > 0): ?>
                <tfoot class="more_reports">
                    <tr>
                        <th colspan="6">    
                            and <?php echo link_to($count, '@reports_list_vehicle?slug=' . $category->getSlug()) ?>
                            more...
                        </th>
                    </tr>
                </tfoot>
            <?php endif; ?>
            <tbody>
                <?php foreach ($category->getOwnReports(sfConfig::get('app_report_max_on_index')) as $i => $report): ?>
                    <?php include_partial('report/report_properties', array('report' => $report)); ?>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

<?php endforeach; ?>


