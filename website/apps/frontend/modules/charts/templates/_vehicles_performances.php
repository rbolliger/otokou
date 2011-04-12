

<?php
foreach ($vehicles as $v) {
    include_partial('vehicle_performances', array('vehicle' => $v));
}
?>
