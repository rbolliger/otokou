
<?php

$menu = new ioMenu(array('id' => 'reports_menu', 'class' => 'vert-menu'));

$menu->addChild('By Vehicle', '@report_index');

$show_all = $sf_user->getAttribute('reports_vehicle_show_more', false);

if($show_all) {
    $menu['By Vehicle']->addChild('Show less...', '', array('class' => 'reports_vehicles_more'));
}

foreach ($vehicles as $vehicle) {

    $class = $vehicle->getIsArchived() ? 'vehicle_archived' : 'vehicle_active';

    $nr = $vehicle->countNewReports();
    if ($nr) {
        $reports = '<span class="vehicle_reports_count">(' . $nr . ' new)</span>';
    } else {
        $reports = '';
    }

    $label = $vehicle->getName() . $reports;

    $menu['By Vehicle']->addChild($label, '@reports_list_vehicle?slug=' . $vehicle->getSlug(), array('class' => $class));
}


if(!$show_all) {
    $menu['By Vehicle']->addChild('Show all...', '', array('class' => 'reports_vehicles_more'));
} 

$menu->addChild('Custom reports', '@reports_list_custom');
$menu->addChild('Create a new report', '@report_new');
echo $menu->render();
?>
