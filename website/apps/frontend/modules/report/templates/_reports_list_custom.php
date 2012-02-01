<?php if (count($custom)) : ?>

    <div class="reports_category" id="category_custom_reports">
        <h3 class="report_category_title"><?php echo link_to('Custom reports', '@reports_list_custom') ?></h3>

        <table class="reports_list">
            <?php echo include_partial('report/reports_list_thead'); ?>
            <?php
            $q = Doctrine_Core::getTable('Report')
                    ->addUsernameQuery($sf_user->getGuardUser()->getUsername())
                    ->andWhere('r.num_vehicles > ?', 1);
            if (($count = Doctrine_Core::getTable('Report')->countOrderedReports($q) - sfConfig::get('app_report_max_on_index')) > 0):
                ?>
                <tfoot class="more_reports">
                    <tr>
                        <th colspan="6">    
                            and <?php echo link_to($count, '@reports_list_custom') ?>
                            more...
                        </th>
                    </tr>
                </tfoot>
            <?php endif; ?>
            <tbody>
                <?php foreach ($custom as $report) : ?>
                    <?php include_partial('report/report_properties', array('report' => $report)); ?>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
<?php endif; ?>