<?php
/**
 * XML Writer Helper to generate the otokou API response XML
 *
 * extends XMLWriter.
 *
 *
 * @author Dave Bergomi
 */
class apiXmlWriter extends XMLWriter
{
	// Constants
	// general XML constants
	const XML_VERSION = '1.0';
	const XML_FORMAT = 'UTF-8';
	const XML_INDENT = ' ';
	const ROOT_ELEMENT_NAME = 'root';
	const MAIN_ELEMENT_NAME = 'otokou';
	const API_VERSION_NAME = 'version';
	const API_VERSION_VALUE = '1.0';
	
	// XML header contants
	const HEADER_ELEMENT_NAME = 'header';
	const ERROR_CODE_ELEMENT_NAME = 'error_code';
	const ERROR_MESSAGE_ELEMENT_NAME = 'error_message';
	const RESPONSE_ELEMENT_NAME = 'response';
	
	// XML body contants
	const BODY_ELEMENT_NAME = 'body';
	const USER_ID_ELEMENT_NAME = 'user_id';
	const FIRST_NAME_ELEMENT_NAME = 'first_name';
	const LAST_NAME_ELEMENT_NAME = 'last_name';
	const USER_UPDATED_AT_ELEMENT_NAME = 'last_user_update';
	const VEHICLES_UPDATED_AT_ELEMENT_NAME = 'last_vehicles_update';
	const VEHICLES_UPDATED_AT_DEFAULT_VALUE = '2000-01-01 00:00:00';
	const VEHICLES_NUMBER_ELEMENT_NAME = 'vehicles_number';
	const VEHICLE_ELEMENT_PREFIXNAME = 'vehicle';
	const VEHICLE_ELEMENT_ATTRIBUTE = 'id';
	const VEHICLE_ID_ELEMENT_PREFIXNAME = 'vehicle_id';
	const VEHICLE_NAME_ELEMENT_PREFIXNAME = 'vehicle_name';
	const RESULT_ELEMENT_NAME = 'result';
	const RESULT_ELEMENT_VALUE = 'ok';
	
	/**
	 * apiXmlWriter() constructor
	 *
	 * Create an apiXmlWriter object instance.
	 */
	public function apiXmlWriter() {
		$this->openMemory();
		$this->setIndent(true);
		$this->setIndentString(self::XML_INDENT);
		$this->startDocument(self::XML_VERSION, self::XML_FORMAT);
	}
	
	/**
	 * start()
	 *
	 * Create the first elements of the XML (root, otokou).
	 *
	 * out:
	 *  - $this: apiXmlWriter instance, for chaining purposes.
	 */
	public function startElements() {
		$this->startElement(self::ROOT_ELEMENT_NAME);
		$this->startElement(self::MAIN_ELEMENT_NAME);
		$this->writeAttribute(self::API_VERSION_NAME, self::API_VERSION_VALUE);
		return $this;
	}
	
	/**
	 * addHeader($errorCode,$errorMessage,$responseType)
	 *
	 * Add the header element.
	 *
	 * in:
	 *  - int $errorCode: error status identifuer (mandatory)
	 *  - string $errorMessage: error message (mandatory)
	 *  - string $responseType: type of response (optional)
	 *
	 * out:
	 *  - $this: apiXmlWriter instance, for chaining purposes.
	 */
	public function addHeader() {
		$this->startElement(self::HEADER_ELEMENT_NAME);
		$arg_list = func_get_args();
		if (func_num_args()==3) {
			$this->writeElement(self::ERROR_CODE_ELEMENT_NAME, $arg_list[0]); 
			$this->writeElement(self::ERROR_MESSAGE_ELEMENT_NAME,  $arg_list[1]);
			$this->writeElement(self::RESPONSE_ELEMENT_NAME, $arg_list[2]); 
		}
		else if (func_num_args()==2) {
			$this->writeElement(self::ERROR_CODE_ELEMENT_NAME,  $arg_list[0]); 
			$this->writeElement(self::ERROR_MESSAGE_ELEMENT_NAME,  $arg_list[1]); 
		}
		$this->endElement();
		return $this;
	}
	
	/**
	 * addBodyGetUser($user)
	 *
	 * Add the body element for the get user response.
	 *
	 * in:
	 *  - $user: doctrine user object instance
	 *
	 * out:
	 *  - $this: apiXmlWriter instance, for chaining purposes.
	 */
	public function addBodyGetUser($user, $vehicles) {
		$this->startElement(self::BODY_ELEMENT_NAME);
		$this->writeElement(self::USER_ID_ELEMENT_NAME, $user->getId());
		$this->writeElement(self::FIRST_NAME_ELEMENT_NAME, $user->getFirstName()); 
		$this->writeElement(self::LAST_NAME_ELEMENT_NAME, $user->getLastName());
		$this->writeElement(self::USER_UPDATED_AT_ELEMENT_NAME, $user->getUpdatedAt());
		$this->writeElement(self::VEHICLES_NUMBER_ELEMENT_NAME, sizeof($vehicles));
		if (sizeof($vehicles) > 0) {
			$vehicles_updated_at = $vehicles[0]->getUpdatedAt();
			for ($i=1;$i<sizeof($vehicles);$i++) {
				if ($vehicles[$i]->getUpdatedAt() > $vehicles_updated_at) {
					$vehicles_updated_at = $vehicles[$i]->getUpdatedAt();
				}
			}
		}
		else {
			$vehicles_updated_at = 'VEHICLES_UPDATED_AT_DEFAULT_VALUE';
		}
		$this->writeElement(self::VEHICLES_UPDATED_AT_ELEMENT_NAME, $vehicles_updated_at); 
		$this->endElement();
		return $this;
	}
	
	/**
	 * addBodyGetVehicles($user,$vehicles)
	 *
	 * Add the body element for the get vehicles response.
	 *
	 * in:
	 *  - $vehicles: doctrine vehicle object instance collection
	 *
	 * out:
	 *  - $this: apiXmlWriter instance, for chaining purposes.
	 */
	public function addBodyGetVehicles($vehicles) {
		$this->startElement(self::BODY_ELEMENT_NAME);
		$this->writeElement(self::VEHICLES_NUMBER_ELEMENT_NAME, sizeof($vehicles));
		for ($i=0;$i<sizeof($vehicles);$i++) {
			$this->startElement(self::VEHICLE_ELEMENT_PREFIXNAME);
			$this->writeAttribute(self::VEHICLE_ELEMENT_ATTRIBUTE, $i);
			$this->writeElement(self::VEHICLE_ID_ELEMENT_PREFIXNAME, $vehicles[$i]->getId());
			$this->writeElement(self::VEHICLE_NAME_ELEMENT_PREFIXNAME, $vehicles[$i]->getName());
			$this->endElement();
		}
		$this->endElement();
		return $this;
	}
	
	/**
	 * addBodySetCharge()
	 *
	 * Add the body element for the set charge response.
	 *
	 * out:
	 *  - $this: apiXmlWriter instance, for chaining purposes.
	 */
	public function addBodySetCharge() {
		$this->startElement(self::BODY_ELEMENT_NAME);
		$this->writeElement(self::RESULT_ELEMENT_NAME, self::RESULT_ELEMENT_VALUE);
		$this->endElement();
		return $this;
	}
	
	/**
	 * endElements()
	 *
	 * End the elements of the XML (root, otokou).
	 *
	 * out:
	 *  - $this: apiXmlWriter instance, for chaining purposes.
	 */
	public function endElements() {
		$this->endElement();
		$this->endElement();
		return $this;
	}
	
	/**
	 * toString()
	 *
	 * return the generated XML in string format.
	 *
	 * out:
	 *  - string $xml: XML in string format.
	 */
	function toString()
	{
		return $this->outputMemory();
	}
}