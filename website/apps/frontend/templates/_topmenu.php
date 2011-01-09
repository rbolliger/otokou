<?php

$menu = new ioMenu(array('id' => 'topmenu1'));
$menu->addChild('Charges', '@charges');
$menu->addChild('Graphs', '');
$menu->addChild('Reports','');
echo $menu->render();

?>


<?php

$menu = new ioMenu(array('id' => 'topmenu2'));
$menu->addChild('Settings','');
$menu->addChild('Login/Logout','');
echo $menu->render();

?>