<?php
slot('leftcol');
include_partial('charts_menu');
end_slot();
?>

<div id="sf_admin_container">

    <h1><?php echo __($title); ?></h1>


    <?php include_partial('filters_menu'); ?>


    <div id="filters">
        <?php include_partial('filters', array('filters' => $filters, 'filters_visibility' => $filters_visibility)); ?>
    </div>

    <?php $gb = $sf_data->getRaw('gb'); ?>
    <?php
    if ($gb) {
        echo $gb->display();
    }
    ?>

    <?php include_partial('debug', array('debug' => $debug)) ?>

</div>