

<?php
foreach ($vehicles as $v) {
    include_partial('charts/vehicle_performances', array('vehicle' => $v));
}
?>
