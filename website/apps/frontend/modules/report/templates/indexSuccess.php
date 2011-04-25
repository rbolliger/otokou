<?php
slot('leftcol');
include_component('report', 'vehiclesMenu');
end_slot();
?>


<?php if (count($reports)) : ?>
    <ul class="reports_list">
    <?php foreach ($reports as $report) : ?>
        <li>
        <?php
        echo link_to($report->getName(), '@report_show?slug='.$report->getSlug());
        echo link_to(image_tag('pdf_icon'),'@report_pdf?slug='.$report->getSlug())
        ?>
        </li>
    <?php endforeach; ?>
    </ul>



<?php else : ?>

            No new reports available. <br>

<?php endif; ?>



