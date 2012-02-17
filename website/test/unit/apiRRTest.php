<?php

require_once dirname(__FILE__).'/../bootstrap/unit.php';
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
new sfDatabaseManager($configuration);
Doctrine::createTablesFromModels(dirname(__FILE__).'/../../lib/model');

$t = new lime_test(12);
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