<style>
    th, td {
        padding: 10px 8px;
        border-bottom: 1px solid black;
    }

    table {
        text-align: right;
        border-collapse: collapse;
        vertical-align: middle;
    }
</style>


<table>
    <thead>
        <tr >
            <th scope="col" style="border-bottom:none;">Vehicle</th>
            <th scope="col" style="border-bottom:none;">Overall cost</th>
            <th scope="col" style="border-bottom:none;">Traveled distance</th>
            <th scope="col" style="border-bottom:none;">Relative cost</th>
            <th scope="col" style="border-bottom:none;">Fuel consumption</th>
        </tr>
        <tr>
            <th style="border-bottom: 2px solid black;">&nbsp;</th>
            <th style="border-bottom: 2px solid black;">[CHF]</th>
            <th style="border-bottom: 2px solid black;">[km]</th>
            <th style="border-bottom: 2px solid black;">[CHF/km]</th>
            <th style="border-bottom: 2px solid black;">[l/100km]</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($vehicles as $v) : ?>
            <tr>
                <th scope="row"><?php echo $v->getName() ?></th>
                <td><?php echo sprintf('%6.0f', $v->getOverallCost()) ?></td>
                <td><?php echo sprintf('%6.0f', $v->getTraveledDistance()) ?></td>
                <td><?php echo sprintf('%6.2f', $v->getCostPerKm()) ?></td>
                <td><?php echo sprintf('%6.2f', $v->getAverageConsumption()) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>