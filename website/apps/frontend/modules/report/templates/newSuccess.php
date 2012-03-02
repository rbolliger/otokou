<?php
slot('leftcol');
include_component('report', 'reportsMenu');
end_slot();
?>





<div id="sf_admin_container">

    <?php slot('content_title') ?>
    <h1>Create a new report</h1>
    <?php end_slot(); ?>
    
    <?php include_partial('charge/flashes') ?>

    <div class="sf_admin_content">

        <?php echo include_partial('report/form', array('form' => $form)); ?>

    </div>
</div>