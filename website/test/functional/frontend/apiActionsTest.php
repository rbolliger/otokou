<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
 
$browser = new OtokouApiTestFunctional(new sfBrowser());
$browser->loadData();


$browser->info('1 - Get User')->
  post('/api/get_user')->
  with('request')->begin()->
    isParameter('module', 'api')->
    isParameter('action', 'getUser')->
  end()->
  with('response')->begin()->
    info('  1.1 - franz is name')->
	matches('/<error_code>110<\/error_code>/')->
  end()
;

$browser->info('1a - Get User Franz')->
  post('/api/get_user',array('request' => '<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>get_user</request>
  </header>
  <body>
   <username>franz</username>
   <apikey>rori123456</apikey>
  </body>
 </otokou>
</root>'))->
  with('request')->begin()->
    isParameter('module', 'api')->
    isParameter('action', 'getUser')->
  end()->
  with('response')->begin()->
    info('  1a.1 - no error')->
	matches('/<error_code>0<\/error_code>/')->
	info('  1a.2 - franz is name')->
	matches('/<first_name>Franz<\/first_name>/')->
  end()
;

$browser->info('2 - Get Vehicles')->
  post('/api/get_vehicles')->
  with('request')->begin()->
    isParameter('module', 'api')->
    isParameter('action', 'getVehicles')->
  end()
;

$browser->info('2a - Get Vehicles of Franz')->
  post('/api/get_vehicles',array('request' => '<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>get_vehicles</request>
  </header>
  <body>
   <username>franz</username>
   <apikey>rori123456</apikey>
  </body>
 </otokou>
</root>'))->
  with('request')->begin()->
    isParameter('module', 'api')->
    isParameter('action', 'getVehicles')->
  end()->
  with('response')->begin()->
    info('  2a.1 - no error')->
	matches('/<error_code>0<\/error_code>/')->
    info('  2a.2 - found 4 vehicles')->
	matches('/<vehicles_number>4<\/vehicles_number>/')->
	 info('  2a.3 - there is an opel caravan')->
	matches('/<vehicle_name>Opel\sAstra\sCaravan\s1\.6<\/vehicle_name>/')->
  end()
;

$browser->info('3 - Set Charge')->
  post('/api/set_charge')->
  with('request')->begin()->
    isParameter('module', 'api')->
    isParameter('action', 'setCharge')->
  end()
;


$browser->info('3a - Set a real Charge')->
  post('/api/set_charge',array('request' => '<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
  <body>
   <username>ruf</username>
   <apikey>rt5674asd0</apikey>
   <vehicle_id>4</vehicle_id>
   <category_id>1</category_id>
   <date>2011-05-02</date>
   <kilometers>1</kilometers>
   <amount>1</amount>
   <comment>comment</comment>
   <quantity>40</quantity>
  </body>
 </otokou>
</root>'))->
  with('request')->begin()->
    isParameter('module', 'api')->
    isParameter('action', 'setCharge')->
  end()->
  with('response')->begin()->
    info('  3a.1 - no error')->
	matches('/<error_code>0<\/error_code>/')->
	 info('  3a.3 - result is ok')->
	matches('/<result>ok<\/result>/')->
  end()
;

$browser->info('4 - Get request')->
 get('/api/set_charge?request=<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
  <body>
   <username>ruf</username>
   <apikey>rt5674asd0</apikey>
   <vehicle_id>4</vehicle_id>
   <category_id>1</category_id>
   <date>2011-05-02</date>
   <kilometers>1</kilometers>
   <amount>1</amount>
   <comment>comment</comment>
   <quantity>40</quantity>
  </body>
 </otokou>
</root>')->
  with('request')->begin()->
    isParameter('module', 'api')->
    isParameter('action', 'setCharge')->
  end()->
  with('response')->begin()->
    info('  4.1 - error for using of get instead of post')->
	matches('/<error_code>2<\/error_code>/')->
  end()
;
