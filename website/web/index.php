<?php

if ($_SERVER['HTTP_HOST'] == 'otokou.donax.ch') {
    require_once(dirname(__FILE__) . '/otokou/config/ProjectConfiguration.class.php');
} else {
    require_once(dirname(__FILE__) . '/../config/ProjectConfiguration.class.php');
}

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
