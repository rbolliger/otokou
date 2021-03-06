


<?php

$menu = new ioMenu(array('id' => 'reports_menu', 'class' => 'vert-menu'));

$menu->addChild('Create a new report', '@report_new');
$menu->addChild('(Un)archive vehicles', '@vehicle');

$nr = Doctrine_Core::getTable('Report')->countNewCustomReports($sf_user->getGuardUser()->getId());
if ($nr) {
    $msg = '<span class="vehicle_reports_count">(' . $nr . ' new)</span>';
} else {
    $msg = '';
}

$menu->addChild('Custom reports'.$msg, '@reports_list_custom');
$menu->addChild('By Vehicle', '@report_index');

foreach ($vehicles as $vehicle) {

    $class = $vehicle->getIsArchived() ? 'vehicle_archived' : 'vehicle_active';

    $nr = $vehicle->countNewOwnReports();
    if ($nr) {
        $reports = get_partial('report/new_reports', array('nr' => $nr));
    } else {
        $reports = '';
    }

    $label = $vehicle->getName() . $reports;

    $menu['By Vehicle']->addChild($label, '@reports_list_vehicle?slug=' . $vehicle->getSlug(), array('class' => $class));
}


echo $menu->render();
?>
