<?php

slot('leftcol');
include_partial('graphs_menu');
end_slot();
?>

<?php

slot('rightcol');
include_partial('filters', array('filters' => $filters));
end_slot();
?>


<h2>Please, select on the left menu a graph you want to display</h2>


<?php include_partial('debug',array('data' => $data,'gb' => $gb)) ?>


