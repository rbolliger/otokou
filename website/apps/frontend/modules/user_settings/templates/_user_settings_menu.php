   
<?php

$menu = new ioMenu(array('id' => 'user_settigns_menu','class' => 'vert-menu'));
$menu->addChild('Account', '@user_settings_account');
$menu->addChild('Change password', '@sf_guard_forgot_password');
$menu->addChild('Manage vehicles', '@vehicle');
echo $menu->render();
?>
