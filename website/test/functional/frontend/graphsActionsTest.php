<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new otokouTestFunctional(new sfBrowser());
$browser->loadData();
$browser->setTester('doctrine', 'sfTesterDoctrine');

$browser->
        
        info('1 - Index')->
        get('/ruf/graphs')->
          with('request')->begin()->
            isParameter('module', 'graphs')->
            isParameter('action', 'index')->
          end()->
          with('response')->begin()->
            isStatusCode(401)->
          end()->

        login('user_graphs','user')->
        get('/graphs')->
          with('response')->begin()->
            isStatusCode(404)->
          end()->
        
        get('/ruf/graphs')->
          with('response')->begin()->
            isStatusCode(403)->
          end()->
        
        get('/user_graphs/graphs')->
          with('request')->begin()->
            isParameter('module', 'graphs')->
            isParameter('action', 'index')->
          end()->
          with('response')->begin()->
            isStatusCode(200)->
          end()
;
