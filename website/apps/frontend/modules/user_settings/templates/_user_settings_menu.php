<h1>User Settings</h1>
   

<?php



$menu = new ioMenu(array('id' => 'user_settigns_menu'));
$menu->addChild('Account','@user_settings_account');
$menu->addChild('Change password','@sf_guard_forgot_password');
$menu->addChild('Charges list', '@user_settings_otokou');
$menu->addChild('Manage vehicles', '@vehicle');
echo $menu->render();

?>
