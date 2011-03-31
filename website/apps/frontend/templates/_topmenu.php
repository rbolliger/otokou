<?php

$menu = new ioMenu(array('id' => 'topmenu1'));
$menu->addChild('Homepage','@homepage_welcome');
$menu->addChild('Charges', '@charge')->requiresAuth(true);
$menu->addChild('Charts', '@chart_index')->requiresAuth(true);
$menu->addChild('Reports','')->requiresAuth(true);
echo $menu->render();

?>


<?php

$menu = new ioMenu(array('id' => 'topmenu2'));
$menu->addChild('Settings','@user_settings_account')->requiresAuth(true);
$menu->addChild('Login','@sf_guard_signin',array('id' => 'login'))->requiresNoAuth(true);
$menu->addChild('Logout','@sf_guard_signout',array('id' => 'logout'))->requiresAuth(true);
echo $menu->render();

?>