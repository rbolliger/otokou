<?php

slot('leftcol');
include_partial('charts_menu');
end_slot();
?>

<?php include_partial('filters', array('filters' => $filters)); ?>

<?php $gb = $sf_data->getRaw('gb');?>
<?php if($gb) { echo $gb->display(); } ?>

<?php include_partial('debug',array('debug' => $debug)) ?>
