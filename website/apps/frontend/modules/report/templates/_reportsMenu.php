
<?php $menu = new ioMenu(array('id' => 'reports_menu', 'class' => 'vert-menu')); 

$menu->addChild('By Vehicle','@report_index');
foreach ($vehicles as $vehicle) {

    $class = $vehicle->getIsArchived() ? 'vehicle_archived' : 'vehicle_active';
    $menu['By Vehicle']->addChild($vehicle->getName(), '@reports_list_vehicle?slug=' . $vehicle->getSlug(),array('class' => $class));
} 

$menu->addChild('Custom reports', '@reports_list_custom');
$menu->addChild('Create a new report', '@report_new'); 
echo $menu->render();
?>
