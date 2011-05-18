<h2><?php echo $chart['title']; ?></h2>

<p><?php echo $chart['comment']; ?></p>

<?php $c = $sf_data->getRaw('chart'); $img = $c['chart']; echo $img->display()."\n"; ?>