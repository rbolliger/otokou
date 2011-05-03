<?php if (in_array(sfConfig::get('sf_environment'), array('dev', 'test'))): ?>


<?php $dbg = $sf_data->getRaw('debug'); ?>


    <h2>Filters values:</h2>

    <table class="debug" id="filter_values">
        <tbody>
    <?php foreach ($dbg['filters'] as $key => $value) : ?>

        <tr>
            <td><?php echo $key ?></td>

        <?php
        if (!$value) {
            $print = 'nothing';
        } elseif (is_array($value)) {
            $print = implode(', ', $value);
            $print = ($print == ', ') ? 'nothing' : $print;
        } else {
            $print = $value;
        }
        ?>


        <td id="<?php echo 'filter_values_' . $key ?>"> <?php echo $print ?></td>

    </tr>
    <?php endforeach; ?>

    <?php if (!$dbg['filters']) : ?>
            <tr>
                <td>No elements found</td>
            </tr>
    <?php endif; ?>
        </tbody></table>

        <h2>Query results:</h2>


        <table class="debug" id="query_results">
            <tbody>
                <?php $gb = $dbg['gb']; ?>
<?php foreach ($query_results = (is_object($gb) ? $gb->getChartsQueryResults() : $gb) as $chart) : ?>

                <tr>
                    <td><?php echo $chart->getId() ?></td>
                    <td><?php echo $chart->getHash() ?></td>
                </tr>

    <?php endforeach; ?>

         
<?php if (!$query_results || !count($query_results) ): ?>
                    <tr>
                        <td>No elements found</td>
                    </tr>
<?php endif; ?>
             </tbody>   </table>


<?php endif ?>