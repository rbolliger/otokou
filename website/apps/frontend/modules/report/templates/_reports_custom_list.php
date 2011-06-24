<?php if (count($custom)) : ?>

    <div class="reports_category" id="category_custom_reports">
        <h2 class="report_category_title"><?php echo link_to('Custom reports', '@reports_list_custom') ?></h2>

        <table class="reports_list">
            <tbody>
                <?php foreach ($custom as $report) : ?>
                    <?php include_partial('report/report_properties', array('report' => $report)); ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php 
        $q = Doctrine_Core::getTable('Report')->addUsernameQuery($sf_user->getGuardUser()->getUsername());
        if (($count = Doctrine_Core::getTable('Report')->countOrderedReports($q) - sfConfig::get('app_report_max_on_index')) > 0): ?>
            <div class="more_reports">
                and <?php echo link_to($count, '@reports_list_custom') ?>
                more...
            </div>
        <?php endif; ?>

    </div>
<?php endif; ?>