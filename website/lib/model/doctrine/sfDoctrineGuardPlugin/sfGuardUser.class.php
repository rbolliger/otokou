<?php

/**
 * sfGuardUser
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    otokou
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class sfGuardUser extends PluginsfGuardUser
{
    
    public function getFullname() {
        $fullname = $this->getName().' '.$this->getLastName();
        
        return $fullname ? $fullname : null;
    }
}
