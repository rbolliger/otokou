<?php

slot('leftcol');
include_partial('charts_menu');
end_slot();
?>

<?php

slot('rightcol');
include_partial('filters', array('filters' => $filters));
end_slot();
?>


<h2>Please, select on the left menu a chart you want to display</h2>


<?php include_partial('debug',array('data' => $data,'gb' => $gb)) ?>


