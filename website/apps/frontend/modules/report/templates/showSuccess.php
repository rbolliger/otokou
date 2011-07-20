<?php slot('leftcol'); ?>
<h2>Download</h2>

<div class="report_download">
    <ul>
        <li><?php echo link_to('Download as pdf','@report_pdf?slug='.$report->getSlug()); ?></li>
    </ul>
</div>


<?php include_component('report', 'reportsMenu'); ?>
<?php end_slot(); ?>


<h1>Overall performances</h1>

<p>The values presented below are calculated overall the entire life period of the vehicle(s).</p>

<?php include_partial('charts/vehicles_performances',array('vehicles' => $report->getVehicles())); ?>



<?php include_partial('report/charts',array('charts' => $charts)); ?>



