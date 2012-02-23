<?php
/**
 * API request/response class
 *
 * the ApiRR class treat a request and generate the appropriate answer
 *
 * list of request:
 * - get the user informations (<body> <apikey> required)
 * - get the user vehicles list (<body> <apikey> required)
 * - add a charge for a car (<body> <apikey>, <user_id>, <vehicle_id>, <date>, <kilometers>, <amount>, <comment>, <quantity> required)
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
class apiRR {
	// Constants codes for requests types
	const GET_USER_REQUEST = 0;
	const GET_VEHICLES_REQUEST = 1;
	const SET_CHARGE_REQUEST = 2;
	const UNDEFINED_REQUEST = 100;
	
	// Constants strings for requests types
	const GET_USER_REQUEST_STRING = "get_user";
	const GET_VEHICLES_REQUEST_STRING = "get_vehicles";
	const SET_CHARGE_REQUEST_STRING = "set_charge";
	
	// request members
	private $rawRequest;
	private $decryptedRequest;
	private $xmlRequest;
	private $requestType;
	private $requestApiVersion;
	private $requestApiKey;
	private $requestVehicle;
	private $requestCategory;
	private $requestDate;
	private $requestKilometers;
	private $requestAmount;
	private $requestComment;
	private $requestQuantity;
	
	// response members
	private $xmlResponse;
	private $decriptedResponse;
	private $rawResponse;
	private $responseUser;
	private $responseVehicles;
	
	// error members
	private $isError;
	private $errorCode;
	private $errorMessage;
	
	/**
	 * apiRR($string,$request_type) constructor
	 *
	 * Create an apiRR object instance.
	 *
	 * in: 
	 *  - (string)$string: received API encrypted message
	 *  - (int)$string: type of API request
	 */
	public function apiRR($string,$request_type) {
		$this->setNoError();
		$this->rawRequest = $string;
		
		switch ($request_type) {
			case self::GET_USER_REQUEST:
			case self::GET_VEHICLES_REQUEST:
			case self::SET_CHARGE_REQUEST:
				$this->requestType = $request_type;
				break;
			default:
				$this->setError(201);
				$this->requestType = self::UNDEFINED_REQUEST;
		}
	}
	
	/**
	 * treatRequest()
	 *
	 * Read API encrypted message and generate a response.
	 */
	public function treatRequest() {
		if ($this->rawRequest=='') $this->setError(110);
		if (!$this->isError) $this->decryptRequest();
		if (!$this->isError) $this->decomposeRequest();
		if (!$this->isError) $this->executeRequest();
		if (!$this->isError) $this->composeResponseXML(); else  $this->composeErrorResponseXML();
		$this->encryptResponse();
	}
	
	/**
	 * decryptRequest()
	 *
	 * Decript received string.
	 */
	private function decryptRequest() {
		$this->decryptedRequest = $this->rawRequest;
	}
	
	/**
	 * decomposeRequest()
	 *
	 * Generate an array from the XML string.
	 */
	private function decomposeRequest() {
		if ($this->xmlRequest = simplexml_load_string($this->decryptedRequest)) {
			$this->decomposeXml();
		}
		else {
			$this->setError(120);
		}
	}
	
	/**
	 * decomposeXml()
	 *
	 * Check and store data extracted from XML.
	 */
	private function decomposeXml() {
		if ($this->xmlRequest->otokou) {
			if ($this->xmlRequest->otokou->attributes()->version) {
				$this->requestApiVersion = $this->xmlRequest->otokou->attributes()->version;
				if ($this->xmlRequest->otokou->header->request) {
					switch ($this->requestType) {
						case self::GET_USER_REQUEST:
							if ($this->xmlRequest->otokou->header->request == self::GET_USER_REQUEST_STRING) {
								$this->decomposeGetUserRequest();
							}
							else $this->setError(210);
							break;
						case self::GET_VEHICLES_REQUEST:
							if ($this->xmlRequest->otokou->header->request == self::GET_VEHICLES_REQUEST_STRING) {
								$this->decomposeGetVehiclesRequest();
							}
							else $this->setError(210);
							break;
						case self::SET_CHARGE_REQUEST:
							if ($this->xmlRequest->otokou->header->request == self::SET_CHARGE_REQUEST_STRING) {
								$this->decomposeSetChargeRequest();
							}
							else $this->setError(210);
							break;
					}
				}
				else $this->setError(132);
			}
			else $this->setError(131);
		}
		else $this->setError(130);
	}
	
	/**
	 * decomposeGetUserRequest()
	 *
	 * Check and store data extracted from XML for a get user request.
	 */
	private function decomposeGetUserRequest() {
		if ($this->xmlRequest->otokou->body) {
			if ($this->xmlRequest->otokou->body->apikey) {
				$this->requestApiKey = $this->xmlRequest->otokou->body->apikey;
			}
			else $this->setError(141);
		}
		else $this->setError(140);
	}
	
	/**
	 * decomposeGetVehiclesRequest()
	 *
	 * Check and store data extracted from XML for a get vehicles request.
	 */
	private function decomposeGetVehiclesRequest() {
		if ($this->xmlRequest->otokou->body) {
			if ($this->xmlRequest->otokou->body->apikey) {
				$this->requestApiKey = $this->xmlRequest->otokou->body->apikey;
			}
			else $this->setError(151);
		}
		else $this->setError(150);
	}
	
	/**
	 * decomposeSetChargeRequest()
	 *
	 * Check and store data extracted from XML for a set charge request.
	 */
	private function decomposeSetChargeRequest() {
		if ($this->xmlRequest->otokou->body) {
			if ($this->xmlRequest->otokou->body->apikey) {
				$this->requestApiKey = $this->xmlRequest->otokou->body->apikey;
				if ($this->xmlRequest->otokou->body->vehicle_id) {
					$this->requestVehicle = $this->xmlRequest->otokou->body->vehicle_id;
					if ($this->xmlRequest->otokou->body->category_id) {
						$this->requestCategory = $this->xmlRequest->otokou->body->category_id;
						if ($this->xmlRequest->otokou->body->date) {
							$this->requestDate = $this->xmlRequest->otokou->body->date;
							if ($this->xmlRequest->otokou->body->kilometers) {
								$this->requestKilometers = $this->xmlRequest->otokou->body->kilometers;
								if ($this->xmlRequest->otokou->body->amount) {
									$this->requestAmount = $this->xmlRequest->otokou->body->amount;
									if ($this->xmlRequest->otokou->body->comment) {
										$this->requestComment = $this->xmlRequest->otokou->body->comment;
										if ($this->xmlRequest->otokou->body->quantity) {
											$this->requestQuantity = $this->xmlRequest->otokou->body->quantity;
										}
										else $this->setError(168);
									}
									else $this->setError(167);
								}
								else $this->setError(166);
							}
							else $this->setError(165);
						}
						else $this->setError(164);
					}
					else $this->setError(163);
				}
				else $this->setError(162);
			}
			else $this->setError(161);
		}
		else $this->setError(160);
	}
	
	/**
	 * executeRequest()
	 *
	 * Execute the received request.
	 */
	private function executeRequest() {
		switch ($this->requestType) {
			case self::GET_USER_REQUEST:
				$user = Doctrine_Core::getTable('sfGuardUser')->createQuery('u')->where('u.api_key = ?',$this->requestApiKey)->execute();
				if (sizeof($user)==1) {
					$this->responseUser = $user[0];
				}
				else $this->setError(211);
				break;
			case self::GET_VEHICLES_REQUEST:
				$user = Doctrine_Core::getTable('sfGuardUser')->createQuery('u')->where('u.api_key = ?',$this->requestApiKey)->execute();
				if (sizeof($user)==1) {
					$this->responseUser =$user[0];
					$this->responseVehicles = Doctrine_Core::getTable('Vehicle')->createQuery('v')->where('v.user_id = ?',$user[0]->getId())->execute();
				}
				else $this->setError(211);
				break;
			case self::SET_CHARGE_REQUEST:
				$user = Doctrine_Core::getTable('sfGuardUser')->createQuery('u')->where('u.api_key = ?',$this->requestApiKey)->execute();
				if (sizeof($user)==1) {
					$this->responseUser =$user[0];
					$this->responseVehicles = Doctrine_Core::getTable('Vehicle')->createQuery('v')->where('v.user_id = ?',$user[0]->getId())->andwhere('v.id = ?',$this->requestVehicle)->execute();
					if (sizeof($this->responseVehicles)==1) {
						$charge = new Charge();
						$charge->setVehicleId($this->responseVehicles[0]->getId());
						$charge->setUserId($this->responseUser->getId());
						$charge->setCategoryId($this->requestCategory);
						$charge->setDate($this->requestDate);
						$charge->setKilometers($this->requestKilometers);
						$charge->setAmount($this->requestAmount);
						$charge->setComment($this->requestComment);
						$charge->setQuantity($this->requestQuantity);
						$charge->save();
					}
					else $this->setError(220);
				}
				else $this->setError(211);
				break;
			default:
				$this->setError(500);
				break;
		}
	}
	
	/**
	 * composeResponseXML()
	 *
	 * Compose the XML response for when not errors have been found.
	 */
	private function composeResponseXML() {
		switch ($this->requestType) {
			case self::GET_USER_REQUEST:
				$this->generateGetUserXml();
			break;
			case self::GET_VEHICLES_REQUEST:
				$this->generateGetVehiclesXml();
			break;
			case self::SET_CHARGE_REQUEST:
				$this->generateSetChargeXml();
			break;
		}
	}
	
	/**
	 * generateGetUserXml()
	 *
	 * Compose the XML response for a get user request.
	 */
	private function generateGetUserXml() {
		$this->xmlResponse = new apiXmlWriter();
		$this->decriptedResponse = $this->xmlResponse->startElements()->addHeader($this->errorCode,$this->errorMessage,self::GET_USER_REQUEST_STRING)->addBodyGetUser($this->responseUser)->endElements()->toString();
	}
	
	/**
	 * generateGetVehiclesXml()
	 *
	 * Compose the XML response for a get vehicles request.
	 */
	private function generateGetVehiclesXml() {
		$this->xmlResponse = new apiXmlWriter();
		$this->decriptedResponse = $this->xmlResponse->startElements()->addHeader($this->errorCode,$this->errorMessage,self::GET_VEHICLES_REQUEST_STRING)->addBodyGetVehicles($this->responseVehicles)->endElements()->toString();
	}
	
	/**
	 * generateSetChargeXml()
	 *
	 * Compose the XML response for a set charge request.
	 */
	private function generateSetChargeXml() {
		$this->xmlResponse = new apiXmlWriter();
		$this->decriptedResponse = $this->xmlResponse->startElements()->addHeader($this->errorCode,$this->errorMessage,self::SET_CHARGE_REQUEST_STRING)->addBodySetCharge()->endElements()->toString();
	}
	
	/**
	 * composeErrorResponseXML()
	 *
	 * Compose the XML response for when errors have been found.
	 */
	private function composeErrorResponseXML() {
		$this->xmlResponse = new apiXmlWriter();
		$this->decriptedResponse = $this->xmlResponse->startElements()->addHeader($this->errorCode,$this->errorMessage)->endElements()->toString();
	}
	
	/**
	 * encryptResponse()
	 *
	 * Encrypt the response XML string.
	 */
	private function encryptResponse() {
		$this->rawResponse = $this->decriptedResponse;
	}
	
	/**
	 * getErrorCode()
	 *
	 * Return the generated response xml code.
	 *
	 * out:
	 *  - (string)xmlCode
	 */
	public function getResponse() {
		return $this->rawResponse;
	}
	
	/**
	 * isError()
	 *
	 * Return if API encountered an error.
	 *
	 * out:
	 *  - (bool)isError
	 */
	public function isError() {
		return $this->isError;
	}
	
	/**
	 * getErrorCode()
	 *
	 * Return the error code.
	 *
	 * out:
	 *  - (int)errorCode
	 */
	public function getErrorCode() {
		return $this->errorCode;
	}
	
	/**
	 * getErrorMessage()
	 *
	 * Return the error message.
	 *
	 * out:
	 *  - (string)errorMessage
	 */
	public function getErrorMessage() {
		return $this->errorMessage;
	}
	
	/**
	 * setNoError()
	 *
	 * Set object status to no error found.
	 */
	private function setNoError() {
		$this->errorCode = 0;
		$this->errorMessage = 'No Error.';
		$this->isError = false;
	}
	
	/**
	 * setNoError($code)
	 *
	 * Set object status to a specified error.
	 *
	 * in: 
	 *  - (int)$code: code of the error
	 */
	private function setError($code) {
		$this->isError = true;
		$this->errorCode = $code;
		switch ($code) {
			case 110:
				$this->errorMessage = 'Empty String.';
				break;
			case 120:
				$this->errorMessage = 'XML not Valid.';
				break;
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
			case 150:
			case 160:
				$this->errorMessage = 'XML not recognized by API, missing body element.';
				break;
			case 141:
			case 151:
			case 161:
				$this->errorMessage = 'XML not recognized by API, missing apikey element.';
				break;
			case 162:
				$this->errorMessage = 'XML not recognized by API, missing vehicle_id element.';
				break;
			case 163:
				$this->errorMessage = 'XML not recognized by API, missing category_id element.';
				break;
			case 164:
				$this->errorMessage = 'XML not recognized by API, missing date element.';
				break;
			case 165:
				$this->errorMessage = 'XML not recognized by API, missing kilometers element.';
				break;
			case 166:
				$this->errorMessage = 'XML not recognized by API, missing amount element.';
				break;
			case 167:
				$this->errorMessage = 'XML not recognized by API, missing comment element.';
				break;
			case 168:
				$this->errorMessage = 'XML not recognized by API, missing quantity element.';
				break;
			case 201:
				$this->errorMessage = 'Undefined Request Type.';
				break;
			case 210:
				$this->errorMessage = 'Wrong API request.';
				break;
			case 211:
				$this->errorMessage = 'User not found.';
				break;
			case 220:
				$this->errorMessage = 'Vehicle not found.';
				break;
			case 500:
			default:
				$this->errorMessage = 'Unknow API error.';
				break;
		}
	}
}

