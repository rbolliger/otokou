<?php
slot('leftcol');
include_component('report', 'vehiclesMenu');
end_slot();
?>


<?php if (count($reports)) : ?>

<?php include_partial('reports_list', array('reports' => $reports)) ?>

<?php else : ?>

        No new reports available. <br>

<?php endif; ?>



