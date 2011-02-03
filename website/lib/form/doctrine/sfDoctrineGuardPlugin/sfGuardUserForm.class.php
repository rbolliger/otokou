<?php

/**
 * sfGuardUser form.
 *
 * @package    otokou
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrinePluginFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfGuardUserForm extends PluginsfGuardUserForm
{
  public function configure()
  {
      
      $this->setValidator('username', new sfValidatorAnd(array(
                    $this->getValidator('username'),
                    new sfValidatorString(array(
                        'required' => true,
                        'trim' => true,
                        'min_length' => 0,
                        'max_length' => 128
                    )),
                    // Usernames should be safe to output without escaping and generally username-like.
                    new sfValidatorRegex(array(
                        'pattern' => '/^\w+$/'
                            ), array('invalid' => 'Usernames must contain only letters, numbers and underscores.'))                    
        )));
      
      
      
      $this->setValidator('password', new sfValidatorString(array(
                    'required' => true,
                    'trim' => true,
                    'min_length' => 6,
                    'max_length' => 128
                        ), array(
                    'min_length' => 'That password is too short. It must contain a minimum of %min_length% characters.')));

        
      
      $this->setValidator('email_address', new sfValidatorAnd(array(
                    $this->getValidator('email_address'),
                    new sfValidatorEmail(array('required' => true, 'trim' => true)),
                    )));
  }
}
