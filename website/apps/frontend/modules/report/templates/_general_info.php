<h1><?php echo $report->getName() ?></h1>

<h2>Overview</h2>

<h3>Range:</h3>
<ul>
    <li>From:
        <?php if ($report->getDateFrom()) : ?>
        <?php echo $report->getDateFrom() ?>
        <?php elseif ($report->getKilometersFrom()) : ?>
        <?php echo $report->getKilometersFrom() ?> km
        <?php endif; ?>
            </li>
            <li>To:
        <?php if ($report->getDateTo()) : ?>
        <?php echo $report->getDateTo() ?>
        <?php elseif ($report->getKilometersTo()) : ?>
        <?php echo $report->getKilometersTo() ?> km
        <?php endif; ?>
                    </li>
</ul>

<h3>Vehicles:</h3>
                <ul>
    <?php foreach ($report->getVehicles() as $v) : ?>
                            <li><?php echo $v->getName() ?></li>
    <?php endforeach; ?>
</ul>