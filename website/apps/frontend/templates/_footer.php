<div class="five columns centered">
<?php

$menu = new ioMenu(array('id' => 'bottommenu1','class' => 'hor-menu'));
$menu->addChild('About Otokou', '');
$menu->addChild('Contact', '');
echo $menu->render();

?>
</div>
