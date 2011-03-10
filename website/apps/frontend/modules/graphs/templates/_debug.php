<?php if (in_array(sfConfig::get('sf_environment'), array('dev', 'test'))): ?>


    <h2>Filters values:</h2>

    <table class="debug" id="filter_values">
        <tbody>
    <?php foreach ($data = $sf_data->getRaw('data') as $key => $value) : ?>

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

    <?php if (!$data) : ?>
            <tr>
                <td>No elements found</td>
            </tr>
    <?php endif; ?>
        </tbody></table>

        <h2>Query results:</h2>


        <table class="debug" id="query_results">
            <tbody>
<?php foreach ($query_results = (is_object($sf_data->getRaw('gb')) ? $gb->getGraphsQueryResults() : $sf_data->getRaw('gb')) as $graph) : ?>

                <tr>
                    <td><?php echo $graph->getId() ?></td>
                    <td><?php echo $graph->getSha() ?></td>
                </tr>

    <?php endforeach; ?>

         
<?php if (!$query_results || !count($query_results) ): ?>
                    <tr>
                        <td>No elements found</td>
                    </tr>
<?php endif; ?>
             </tbody>   </table>


<?php endif ?>