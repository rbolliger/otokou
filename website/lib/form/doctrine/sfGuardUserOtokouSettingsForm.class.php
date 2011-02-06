<?php


class sfGuardUserOtokouSettingsForm extends sfGuardUserForm
{
  public function configure()
  {
      
      parent::configure();
      
      $this->useFields(array('list_max_per_page'));
      

  }

}
