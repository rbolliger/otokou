<?php


class sfGuardUserAccountForm extends sfGuardUserForm
{
  public function configure()
  {
      
      parent::configure();
      
      $this->useFields(array('first_name','last_name','email_address'));
      

  }

}
