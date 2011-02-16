<?php if (in_array(sfConfig::get('sf_environment'), array('dev', 'test'))): ?>


    <h2>Filters values:</h2>

    <table class="debug" id="filter_values">

    <?php foreach ($sf_data->getRaw('data') as $key => $value) : ?>

        <tr>
            <td><?php echo $key ?></td>
            <td><?php echo is_array($value) ? implode(', ',$value) : $value ?></td>
        </tr>

    <?php endforeach; ?>
    </table>

<h2>Query results:</h2>


<table class="debug" id="query_results">

    <?php foreach ($query_results as $graph) : ?>

        <tr>
            <td><?php echo $graph->getId() ?></td>
            <td><?php echo $graph->getSha() ?></td>
        </tr>

    <?php endforeach; ?>

        <?php if(!count($query_results)) : ?>
        <tr>
            <td>No elements found</td>
        </tr>
        <?php        endif;?>
    </table>


<?php endif ?>