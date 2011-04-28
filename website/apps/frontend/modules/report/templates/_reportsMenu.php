<h2>Vehicles</h2>

<?php $menu = new ioMenu(array('class' => 'vehicles_menu')); ?>

<?php
foreach ($vehicles as $vehicle) {

    $class = $vehicle->getIsArchived() ? 'vehicle_archived' : 'vehicle_active';
    $menu->addChild($vehicle->getName(), '@reports_list_vehicle?slug=' . $vehicle->getSlug(),array('class' => $class));
} ?>


<?php echo $menu->render(); ?>

<h2>Custom reports</h2>

<?php $menu2 = new ioMenu(array('class' => 'custom_reports_menu')); ?>
<?php $menu2->addChild('Custom reports', '@reports_list_custom',array('class' => 'vehicle_active')); ?>
<?php echo $menu2->render(); ?>


<h2>Create a new report</h2>

<?php $menu3 = new ioMenu(array('class' => 'report_new')); ?>
<?php $menu3->addChild('New report', '@report_new',array('class' => 'vehicle_active')); ?>
<?php echo $menu3->render(); ?>