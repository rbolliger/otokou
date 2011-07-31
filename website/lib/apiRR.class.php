<?php
/**
 * API request/response class
 *
 * the ApiRR class treat a request and generate the appropriate answer
 *
 * list of request:
 * - get the user informations
 * - get the user cars list
 * - add a charge for a car
 *
 *
 * @author Dave Bergomi
 */
class apiRR {

	private $request;
	private $response;

    public function apiRR($string) {
        $this->request = $string;
    }
	
	public function getResponse() {
		return $this->response;
	}
	
	public function treatRequest() {
		if ($this->request == '' ) {
			$this->response = 'otokou API error';
		}
		else {
			$this->response = $this->request;
		}
	}
}

