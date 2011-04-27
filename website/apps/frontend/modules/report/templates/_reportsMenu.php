<h2>Vehicles</h2>

<?php $menu = new ioMenu(array('class' => 'vehicles_menu')); ?>

<?php
foreach ($vehicles as $vehicle) {

    $class = $vehicle->getIsArchived() ? 'vehicle_archived' : 'vehicle_active';
    $menu->addChild($vehicle->getName(), '@reports_list?slug=' . $vehicle->getSlug(),array('class' => $class));
} ?>


<?php echo $menu->render(); ?> 