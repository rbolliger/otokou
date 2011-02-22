<?php

class myUser extends sfGuardSecurityUser
{

    public function signOut() {

        $this->getAttributeHolder()->removeNamespace('admin_module');
        $this->getAttributeHolder()->removeNamespace('graphs');
        

        parent::signOut();

        
    }

}
