<?php

$cr = $vehicle->countOwnReports();
$cnr = $vehicle->countNewOwnReports();

if ($cr) {
    $msg = $cr . ' report'.(($cr > 1) ? 's' : '');
} else {
    $msg = 'No reports';
}

if ($cnr) {
    $new = get_partial('report/new_reports', array('nr' => $cnr));
} else {
    $new = '';
}

echo link_to($msg, '@reports_list_vehicle?slug=' . $vehicle->getSlug()).$new ;
?>
