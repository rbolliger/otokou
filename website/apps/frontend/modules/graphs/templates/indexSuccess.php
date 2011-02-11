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


<?php print_r($data); ?>