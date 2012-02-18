


<?php

$menu = new ioMenu(array('id' => 'reports_menu', 'class' => 'vert-menu'));

$menu->addChild('Create a new report', '@report_new');
$menu->addChild('Custom reports', '@reports_list_custom');
$menu->addChild('By Vehicle', '@report_index');

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


echo $menu->render();
?>
