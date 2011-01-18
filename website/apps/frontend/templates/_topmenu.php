<?php

$menu = new ioMenu(array('id' => 'topmenu1'));
$menu->addChild('Homepage','@homepage_welcome');
$menu->addChild('Charges', '@charges');
$menu->addChild('Graphs', '');
$menu->addChild('Reports','');
echo $menu->render();

?>


<?php

$menu = new ioMenu(array('id' => 'topmenu2'));
$menu->addChild('Settings','');
$menu->addChild('Login','@sf_guard_signin',array('id' => 'login'))->requiresNoAuth(true);
$menu->addChild('Logout','@sf_guard_signout',array('id' => 'logout'))->requiresAuth(true);
echo $menu->render();

?>