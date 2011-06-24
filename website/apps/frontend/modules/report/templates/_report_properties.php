<tr class="<?php echo $report->getIsNew() ? 'report_new' : 'report_old' ?>">
    <td><?php echo $report->getIsNew() ? image_tag('icons/star') : '&nbsp;' ?></td>
    <td><?php echo link_to(image_tag('icons/page_white_acrobat'), '@report_pdf?slug=' . $report->getSlug()) ?></td>
    <td><?php echo link_to($report->getName(), '@report_show?slug=' . $report->getSlug()); ?></td>


    <td>
        <?php
        if ($report->getDateFrom()) {
            $from = $report->getDateFrom();
        } elseif ($report->getKilometersFrom()) {
            $from = $report->getKilometersFrom() . ' km';
        } else {
            $from = '&nbsp;';
        }
        echo $from;
        ?>
    </td>
    <td>
        <?php
        if ($report->getDateTo()) {
            echo $report->getDateTo();
        } elseif ($report->getKilometersTo()) {
            echo $report->getKilometersTo() . ' km';
        } else {
            echo '&nbsp;';
        }
        ?>
    </td>
    <td>
        <?php
        $vehicles = $report->getVehicles();
        $nv = count($vehicles);
        foreach ($vehicles as $key => $v) {
            echo $v->getName();
            if ($key < $nv - 1) {
                echo ', ';
            }
        }
        ?>
    </td>

    <td>
        <?php echo link_to(image_tag('icons/delete'), '@report_delete?slug=' . $report->getSlug(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'absolute' => true)) ?>
    </td>
</tr>