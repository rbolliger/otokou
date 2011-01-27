<?php

/**
 * charge module configuration.
 *
 * @package    otokou
 * @subpackage charge
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: configuration.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class chargeGeneratorConfiguration extends BaseChargeGeneratorConfiguration
{
    protected $user_id;
    
    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }
    
    public function getUserId() {
        return $this->user_id;
    }
    
    
    public function getForm($object = null, $options = array()) {
        
        if (null === $object) {
        $object = new Charge;
        }
        
        $object->setUserId($this->getUserId());
        
        
        return parent::getForm($object,$options);
        
    }
}
