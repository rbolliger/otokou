<?php slot('leftcol'); ?>
<?php
$menu = new ioMenu(array('id' => 'charge_new_menu','class' => 'vert-menu'));
$menu->addChild('Add a new vehicle', '@vehicle_new');
echo $menu->render();
?>
<?php end_slot(); ?>
