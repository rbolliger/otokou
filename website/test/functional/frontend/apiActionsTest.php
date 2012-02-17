<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
 
$browser = new OtokouTestFunctional(new sfBrowser());
$browser->loadData();


$browser->info('1 - Get User')->
  get('/api/get_user')->
  with('request')->begin()->
    isParameter('module', 'api')->
    isParameter('action', 'getUser')->
  end()
;

/*
$browser->
  get('/api/get_user?request=<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <error>000</error>
   <request>get_user</request>
  </header>
  <body>
   <firstname>asdrubale</firstname>
   <lastname>arnaldo</lastname>
  </body>
 </otokou>
</root>')->
  with('request')->begin()->
    isParameter('module', 'api')->
    isParameter('action', 'getUser')->
  end()->
  with('response')->begin()->
    info('  1.1 - asdrubale is name')->
    checkElement('firstname', "Franz")->
  end()
;*/

$browser->info('2 - Get Vehicles')->
  get('/api/get_vehicles')->
  with('request')->begin()->
    isParameter('module', 'api')->
    isParameter('action', 'getVehicles')->
  end()
;

$browser->info('3 - Set Charge')->
  get('/api/set_charge')->
  with('request')->begin()->
    isParameter('module', 'api')->
    isParameter('action', 'setCharge')->
  end()
;
