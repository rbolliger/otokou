<?php
/**
 * API request/response class
 *
 * the ApiRR class treat a request and generate the appropriate answer
 *
 * list of request:
 * - get the user informations (api?request=get_user,rori123456)
 * - get the user vehicles list (api?request=get_vehicles,rori123456)
 * - add a charge for a car (api?request=set_charge,rt5674asd0,2,1,2011-05-02,20,30,comment,40)
 *
 * error codes:
 * - 000 No Error
 * - 1XX Syntax Error
 * - 2XX Parameters Error
 * - 5XX API Error
 *
 * TODO:
 * - tests
 * - doc
 * - encrypt/decript
 * - replace csv
 *
 *
 * @author Dave Bergomi
 */
 
/* examples

---outputs
-get user

<?xml version="1.0" encoding="UTF-8"?>
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
</root>



---inputs
-get user

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

*/
class apiRR {
	const GET_USER_REQUEST = 0;
	const GET_VEHICLES_REQUEST = 1;
	const SET_CHARGE_REQUEST = 2;
	const UNDEFINED_REQUEST = 100;
	
	const GET_USER_REQUEST_STRING = "get_user";
	const GET_VEHICLES_REQUEST_STRING = "get_vehicles";
	const SET_CHARGE_REQUEST_STRING = "set_charge";
	
	private $rawRequest;
	private $decryptedRequest;
	private $requestType;
	private $xml;
	
	private $requestApiVersion;
	private $requestUser;
	private $requestVehicle;
	private $requestCategory;
	private $requestDate;
	private $requestKilometers;
	private $requestAmount;
	private $requestComment;
	private $requestQuantity;
	
	private $decriptedResponse;
	private $rawResponse;
	
	private $isError;
	private $errorCode;
	private $errorMessage;
	
	public function apiRR($string,$request_type) {
        $this->rawRequest = $string;
		$this->errorCode = '000';
		$this->errorMessage = 'No Error.';
		$this->isError = false;
		switch ($request_type) {
			case self::GET_USER_REQUEST:
			case self::GET_VEHICLES_REQUEST:
			case self::SET_CHARGE_REQUEST:
				$this->requestType = $request_type;
				break;
			default:
				$this->errorCode = '201';
				$this->errorMessage = 'Undefined Request Type.';
				$this->requestType = self::UNDEFINED_REQUEST;
		}
	}
	
	public function isError() {
		return $this->isError;
	}
	
	public function getErrorCode() {
		return $this->errorCode;
	}
	
	public function getErrorMessage() {
		return $this->errorMessage;
	}
	
	public function getResponse() {
		return $this->rawResponse;
	}
	
	public function treatRequest() {
		if ($this->rawRequest == '' ) {
			$this->errorCode = '110';
			$this->errorMessage = 'Empty String.';
			$this->isError = true;
		}
		
		if (!$this->isError) {
			$this->decryptRequest();
		}
		
		if (!$this->isError) {
			$this->decomposeRequest();
		}
		
		if (!$this->isError) {
			$this->executeRequest();
		}
		
		$this->composeResponse();
		
		$this->encryptResponse();
	}
	
	private function decryptRequest() {
		$this->decryptedRequest = $this->rawRequest;
	}
	
	private function decomposeRequest() {
		if ($this->xml = simplexml_load_string($this->decryptedRequest)) {
			$this->decomposeXml();
		}
		else {
			$this->errorCode = '120';
			$this->errorMessage = 'XML not Valid.';
			$this->isError = true;
		}
		
		/*
		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			$components = str_getcsv($this->decryptedRequest);
		}
		else {
			$components = split(",", $this->decryptedRequest);
		}
		
		if (sizeof($components) < 2 && sizeof($components) > 3) {
			$this->errorCode = '120';
			$this->errorMessage = 'Unknow Request Format';
			$this->isError = true;
		}
		else {
			if ($components[0] == 'get_user') {
				if (sizeof($components) != 2) {
					$this->errorCode = '140';
					$this->errorMessage = 'Wrong number of parameters for get_user Request Type';
					$this->isError = true;
				}
				else {
					$this->requestType = self::GET_USER_REQUEST;
					$this->requestUser=$components[1];
				}
			}
			else if ($components[0] == 'get_vehicles') {
				if (sizeof($components) != 2) {
					$this->errorCode = '141';
					$this->errorMessage = 'Wrong number of parameters for get_cars Request Type';
					$this->isError = true;
				}
				else {
					$this->requestType = self::GET_VEHICLE_REQUEST;
					$this->requestUser=$components[1];
				}
			}
			else if ($components[0] == 'set_charge') {
				if (sizeof($components) != 9) {
					$this->errorCode = '142';
					$this->errorMessage = 'Wrong number of parameters for set_charge Request Type';
					$this->isError = true;
				}
				$this->requestType = self::SET_CHARGE_REQUEST;
				$this->requestUser=$components[1];
				$this->requestVehicle=$components[2];
				$this->requestCategory=$components[3];
				$this->requestDate=$components[4];
				$this->requestKilometers=$components[5];
				$this->requestAmount=$components[6];
				$this->requestComment=$components[7];
				$this->requestQuantity=$components[8];
			}
			else {
				$this->errorCode = '130';
				$this->errorMessage = 'Unknow Request Type';
				$this->isError = true;
			}
			
			$this->requestParameters = $components[1];
		}
		*/
	}
	
	private function decomposeXml() {
		if ($this->xml->otokou) {
			if ($this->xml->otokou->attributes()->version) {
				$this->requestApiVersion = $this->xml->otokou->attributes()->version;
				if ($this->xml->otokou->header->request) {
					switch ($this->requestType) {
						case self::GET_USER_REQUEST:
							if ($this->xml->otokou->header->request == self::GET_USER_REQUEST_STRING) {
								$this->decomposeGetUserRequest();
							}
							else {
								$this->setError(210);
							}
							break;
						case self::GET_VEHICLES_REQUEST:
							if ($this->xml->otokou->header->request === self::GET_VEHICLES_REQUEST_STRING) {
								$this->decomposeGetVehiclesRequest();
							}
							else {
								$this->setError(210);
							}
							break;
						case self::SET_CHARGE_REQUEST:
							if ($this->xml->otokou->header->request === self::SET_CHARGE_REQUEST_STRING) {
								$this->decomposeSetChargeRequest();
							}
							else {
								$this->setError(210);
							}
							break;
					}
				}
				else {
					$this->setError(132);
				}
			}
			else {
				$this->setError(131);
			}
		}
		else {
			$this->setError(130);
		}
		
	}
	
	private function decomposeGetUserRequest() {
		if ($this->xml->otokou->body) {
			if ($this->xml->otokou->body->apikey) {
				$this->requestUser = $this->xml->otokou->body->apikey;
			}
			else {
				$this->setError(141);
			}
		}
		else {
			$this->setError(140);
		}
	}
	
	private function decomposeGetVehiclesRequest() {
		
	}
	
	private function decomposeSetChargeRequest() {
		
	}
	
	private function setError($code) {
		$this->isError = true;
		$this->errorCode = $code;
		switch ($code) {
			case 130:
				$this->errorMessage = 'XML not recognized by API, missing otokou element.';
				break;
			case 131:
				$this->errorMessage = 'XML not recognized by API, missing otokou attribute version.';
				break;
			case 132:
				$this->errorMessage = 'XML not recognized by API, missing request element.';
				break;
			case 140:
				$this->errorMessage = 'XML not recognized by API, missing body element.';
				break;
			case 141:
				$this->errorMessage = 'XML not recognized by API, missing apikey element.';
				break;
			case 210:
				$this->errorMessage = 'Wrong API request.';
				break;
			default:
				$this->errorMessage = 'General error.';
				break;
		}
	}
	
	private function executeRequest() {
		
		switch ($this->requestType) {
			case self::GET_USER_REQUEST:
				$user = Doctrine_Core::getTable('sfGuardUser')->createQuery('u')->where('u.api_key = ?',$this->requestUser)->execute();
				if (sizeof($user)==1) {
					$this->decriptedResponse=$user[0]->getId().','.$user[0]->getFirstName().','.$user[0]->getLastName();
				}
				else {
					$this->errorCode = '211';
					$this->errorMessage = 'User not found';
					$this->isError = true;
				}
				break;
				/*
			case self::GET_VEHICLE_REQUEST:
				//$cars = Doctrine_Core::getTable('Vehicle')->createQuery('v')->leftJoin('v.User u')->where('u.api_key = ?',$this->requestUser)->execute();
				$user = Doctrine_Core::getTable('sfGuardUser')->createQuery('u')->where('u.api_key = ?',$this->requestUser)->execute();
				if (sizeof($user)==1) {
					$vehicles = Doctrine_Core::getTable('Vehicle')->createQuery('v')->where('v.user_id = ?',$user[0]->getId())->execute();
					$this->decriptedResponse = sizeof($vehicles);
					foreach ($vehicles as $vehicle) {
						$this->decriptedResponse .=','.$vehicle->getId().','.$vehicle->getName();
					}
				}
				else {
					$this->errorCode = '210';
					$this->errorMessage = 'User not found';
					$this->isError = true;
				}
				break;
			case self::SET_CHARGE_REQUEST:
				$user = Doctrine_Core::getTable('sfGuardUser')->createQuery('u')->where('u.api_key = ?',$this->requestUser)->execute();
				if (sizeof($user)==1) {
					$vehicle = Doctrine_Core::getTable('Vehicle')->createQuery('v')->where('v.user_id = ?',$user[0]->getId())->andwhere('v.id = ?',$this->requestVehicle)->execute();
					if (sizeof($vehicle)==1) {
						$charge = new Charge();
						$charge->setVehicleId($vehicle[0]->getId());
						$charge->setUserId($user[0]->getId());
						$charge->setCategoryId($this->requestCategory);
						$charge->setDate($this->requestDate);
						$charge->setKilometers($this->requestKilometers);
						$charge->setAmount($this->requestAmount);
						$charge->setComment($this->requestComment);
						$charge->setQuantity($this->requestQuantity);
						$charge->save();
						$this->decriptedResponse = "New Charge Saved";						
					}
					else {
						$this->errorCode = '220';
						$this->errorMessage = 'Vehicle not found';
						$this->isError = true;
					}	
				}
				else {
					$this->errorCode = '210';
					$this->errorMessage = 'User not found';
					$this->isError = true;
				}
				break;
				*/
			default:
				$this->errorCode = '500';
				$this->errorMessage = 'Unknow API error';
				$this->isError = true;
				break;
		}
	}
	
	private function composeResponse() {
		$this->decriptedResponse = $this->errorCode.','.$this->errorMessage.','.$this->decriptedResponse;
	}
	
	private function encryptResponse() {
		$this->rawResponse = $this->decriptedResponse;
	}
}

