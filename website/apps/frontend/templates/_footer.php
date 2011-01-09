<?php

$menu = new ioMenu(array('id' => 'topmenu1'));
$menu->addChild('About Otokou', '');
$menu->addChild('Contact', '');
echo $menu->render();

?>
