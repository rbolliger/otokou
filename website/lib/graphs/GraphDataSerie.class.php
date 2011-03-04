<?php
/**
 * Description of GraphDataSerie
 *
 * @author Raffaele Bolliger <raffaele.bolliger at gmail.com>
 */
class GraphDataSerie {

    protected $params;
    protected static $fields = array('raw_data','id','label');


    public function  __construct(array $params) {

        $fields = self::$fields;

        foreach ($fields as $name) {

            if (isset($params[$name])) {
                $this->setParameter($name, $params[$name]);
            }
        }

        if ($diff = array_diff(array_keys($params),$fields)) {
            throw new sfException(sprintf('Following fields are not accepted by %s: %s',get_class($this),implode(', ',$diff)));
        }
    }

    public function setParameters(array $params) {
        $this->params = $params;
    }

    public function setParameter($name,$value) {
        
        $this->params[$name] = $value;
    }

    public function getParameter($name) {

        if (isset($this->params[$name])) {
            return $this->params[$name];
        }

        throw new sfException(sprintf('Parameter %s is not set',$name));
    }

    public function setId($value) {

        $this->setParameter('id',$value);
    }

    public function setLabel($value) {

        $this->setParameter('label',$value);
    }

    public function setRawData($value) {

        $this->setParameter('raw_data',$value);
    }


    public function getId() {

        return $this->getParameter('id');
    }

    public function getLabel() {

        return $this->getParameter('label');
    }

    public function getRawData() {

        return $this->getParameter('raw_data');
    }



}
