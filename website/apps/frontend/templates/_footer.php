<?php

$menu = new ioMenu(array('id' => 'bottommenu1'));
$menu->addChild('About Otokou', '');
$menu->addChild('Contact', '');
echo $menu->render();

?>
