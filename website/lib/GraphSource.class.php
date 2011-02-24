<?php
/**
 * Description of GraphSource
 *
 * @author Raffaele Bolliger (raffaele.bolliger at gmail.com)
 */
class GraphSource {

    protected $parameters = array();

    public function  __construct() {
        
    }


    public function setParam($param,$value) {
        $this->parameters[$param] = $value;
    }
    
    
    public function getParam($param,$default = null) {
        
        return isset($this->parameters[$param]) ? $this->parameters[$param] : $default;
    }




}


