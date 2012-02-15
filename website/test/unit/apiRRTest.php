<?php

require_once dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(4);
$t->comment('errors');
$api = new ApiRR("",ApiRR::GET_USER_REQUEST);
$api->treatRequest();
$t->is($api->isError(), true, 'detects empty string error in get user request');
unset($api);

$api = new ApiRR("",ApiRR::GET_VEHICLE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 110, 'recognises empty string error in get vehicle request');
unset($api);

$api = new ApiRR("",ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorMessage(), 'Empty String', 'gives right error message for empty string error in set charge request');
unset($api);

$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
  </header>
  <body>
  </body>
 </otokou>
</root>
 ',5);
 $t->is($api->getErrorCode(), 201, 'recognises wrogn request type error');
 unset($api);