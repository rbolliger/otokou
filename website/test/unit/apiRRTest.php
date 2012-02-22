<?php

require_once dirname(__FILE__).'/../bootstrap/unit.php';
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
new sfDatabaseManager($configuration);
Doctrine::createTablesFromModels(dirname(__FILE__).'/../../lib/model');
Doctrine_Core::loadData(sfConfig::get('sf_test_dir').'/fixtures2');

$t = new lime_test(27);
$t->comment('-- errors');

$t->comment('- Constructor parameters Errors');
// 1
$api = new ApiRR("",ApiRR::GET_USER_REQUEST);
$api->treatRequest();
$t->is($api->isError(), true, 'detects empty string error in get user request');
unset($api);

// 2
$api = new ApiRR("",ApiRR::GET_VEHICLES_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 110, 'recognises empty string error in get vehicle request');
unset($api);

// 3
$api = new ApiRR("",ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorMessage(), 'Empty String.', 'gives right error message for empty string error in set charge request');
unset($api);

// 4
$api = new ApiRR('
<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>get_user</request>
  </header>
  <body>
   <apikey>rori123456</apikey>
  </body>
 </otokou>
</root>
',5);
$t->is($api->getErrorCode(), 201, 'recognises wrong request type error');
unset($api);

$t->comment('- XML syntax Errors foe all requests');
// 5
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>get_user</request>
  </header>
  <body>
   <apikey>rori123456</apikey>
  </body>
 </otokous>
</root>
',ApiRR::GET_USER_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 120, 'recognises wrong XML format');
unset($api);

// 6
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokous version="1.0">
  <header>
   <request>get_user</request>
  </header>
  <body>
   <apikey>rori123456</apikey>
  </body>
 </otokous>
</root>
',ApiRR::GET_USER_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 130, 'gives right error message for not finding otokou element');
unset($api);

// 7
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou>
  <header>
   <request>get_user</request>
  </header>
  <body>
   <apikey>rori123456</apikey>
  </body>
 </otokou>
</root>
',ApiRR::GET_USER_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 131, 'gives right error message for not finding version attribute');
unset($api);

// 8
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <requet>get_user</requet>
  </header>
  <body>
   <apikey>rori123456</apikey>
  </body>
 </otokou>
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 132, 'gives right error message for not finding request element');
unset($api);

// 9
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>get_user</request>
  </header>
  <body>
   <apikey>rori123456</apikey>
  </body>
 </otokou>
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 210, 'gives right error message for request element not corresponding to request action');
unset($api);

$t->comment('- XML Errors for get_user');
// 10
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>get_user</request>
  </header>
 </otokou>
</root>
',ApiRR::GET_USER_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 140, 'gives right error message for not finding body element');
unset($api);

// 11
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>get_user</request>
  </header>
  <body>
  </body>
 </otokou>
</root>
',ApiRR::GET_USER_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 141, 'gives right error message for not finding apikey element');
unset($api);

// 12
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>get_user</request>
  </header>
  <body>
   <apikey>rori123436</apikey>
  </body>
 </otokou>
</root>
',ApiRR::GET_USER_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 211, 'gives right error message for an unexisting api key');
unset($api);

$t->comment('- XML Errors for get_vehicles');
// 13
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>get_vehicles</request>
  </header>
 </otokou>
</root>
',ApiRR::GET_VEHICLES_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 150, 'gives right error message for not finding body element');
unset($api);

// 14
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>get_vehicles</request>
  </header>
  <body>
  </body>
 </otokou>
</root>
',ApiRR::GET_VEHICLES_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 151, 'gives right error message for not finding apikey element');
unset($api);

// 15
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>get_vehicles</request>
  </header>
  <body>
   <apikey>rori123436</apikey>
  </body>
 </otokou>
</root>
',ApiRR::GET_VEHICLES_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 211, 'gives right error message for an unexisting api key');
unset($api);

$t->comment('- XML Errors for set_charge');
// 16
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
 </otokou>
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 160, 'gives right error message for not finding body element');
unset($api);

// 17
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
  <body>
  </body>
 </otokou>
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 161, 'gives right error message for not finding apikey element');
unset($api);

// 18
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
  <body>
   <apikey>rt5674asd0</apikey>
  </body>
 </otokou>
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 162, 'gives right error message for not finding vehicle_id element');
unset($api);

// 19
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
  <body>
   <apikey>rt5674asd0</apikey>
   <vehicle_id>2</vehicle_id>
  </body>
 </otokou>
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 163, 'gives right error message for not finding category_id element');
unset($api);

// 20
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
  <body>
   <apikey>rt5674asd0</apikey>
   <vehicle_id>2</vehicle_id>
   <category_id>1</category_id>
  </body>
 </otokou>
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 164, 'gives right error message for not finding date element');
unset($api);

// 21
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
  <body>
   <apikey>rt5674asd0</apikey>
   <vehicle_id>2</vehicle_id>
   <category_id>1</category_id>
   <date>1</date>
  </body>
 </otokou>
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 165, 'gives right error message for not finding kilometers element');
unset($api);

// 22
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
  <body>
   <apikey>rt5674asd0</apikey>
   <vehicle_id>2</vehicle_id>
   <category_id>1</category_id>
   <date>1</date>
   <kilometers>1</kilometers>
  </body>
 </otokou>
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 166, 'gives right error message for not finding amount element');
unset($api);

// 23
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
  <body>
   <apikey>rt5674asd0</apikey>
   <vehicle_id>2</vehicle_id>
   <category_id>1</category_id>
   <date>1</date>
   <kilometers>1</kilometers>
   <amount>1</amount>
  </body>
 </otokou>
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 167, 'gives right error message for not finding comment element');
unset($api);

// 24
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
  <body>
   <apikey>rt5674asd0</apikey>
   <vehicle_id>2</vehicle_id>
   <category_id>1</category_id>
   <date>1</date>
   <kilometers>1</kilometers>
   <amount>1</amount>
   <comment>comment</comment>
  </body>
 </otokou>
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 168, 'gives right error message for not finding quantity element');
unset($api);

// 25
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
  <body>
   <apikey>rt5674asd30</apikey>
   <vehicle_id>2</vehicle_id>
   <category_id>1</category_id>
   <date>1</date>
   <kilometers>1</kilometers>
   <amount>1</amount>
   <comment>comment</comment>
   <quantity>40</quantity>
  </body>
 </otokou>
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 211, 'gives right error message for an unexisting api key');
unset($api);

// 26
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
  <body>
   <apikey>rt5674asd0</apikey>
   <vehicle_id>123</vehicle_id>
   <category_id>1</category_id>
   <date>1</date>
   <kilometers>1</kilometers>
   <amount>1</amount>
   <comment>comment</comment>
   <quantity>40</quantity>
  </body>
 </otokou>
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 220, 'gives right error message for an unexisting vehicle id');
unset($api);

// 27
$api = new ApiRR('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <otokou version="1.0">
  <header>
   <request>set_charge</request>
  </header>
  <body>
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
</root>
',ApiRR::SET_CHARGE_REQUEST);
$api->treatRequest();
$t->is($api->getErrorCode(), 0, 'gives no error message for right API call');
unset($api);
