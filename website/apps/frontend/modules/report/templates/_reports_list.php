<ul class="reports_list">
    <?php foreach ($reports as $report) : ?>
        <li class="<?php echo $report->getIsNew() ? 'report_new' : 'report_old'; ?>">
        <?php
        echo link_to($report->getName(), '@report_show?slug=' . $report->getSlug());
        echo link_to(image_tag('pdf_icon'), '@report_pdf?slug=' . $report->getSlug())
        ?>
    </li>
    <?php endforeach; ?>
</ul>
