
<?php $menu = new ioMenu(array('id' => 'vehicles_menu', 'class' => 'vert-menu')); 

$menu->addChild('By Vehicle','@report_index');
foreach ($vehicles as $vehicle) {

    $class = $vehicle->getIsArchived() ? 'vehicle_archived' : 'vehicle_active';
    $menu['By Vehicle']->addChild($vehicle->getName(), '@reports_list_vehicle?slug=' . $vehicle->getSlug(),array('class' => $class));
} 

$menu->addChild('Custom reports', '@reports_list_custom',array('class' => 'vehicle_active'));
$menu->addChild('Create a new report', '@report_new',array('class' => 'vehicle_active')); 
echo $menu->render();
?>
