<?php

class sfApplyApplyForm extends sfGuardUserForm {

    private $validate = null;

    public function configure() {
        parent::configure();
        
        // We're making a new user or editing the user who is
        // logged in. In neither case is it appropriate for
        // the user to get to pick an existing userid. The user
        // also doesn't get to modify the validate field which
        // is part of how their account is verified by email.
       
        unset($this['user_id']);
        
        $this->useFields(array('username','first_name','last_name','password','email_address'));

        // Add username and password fields which we'll manage
        // on our own. Before you ask, I experimented with separately 
        // emitting, merging or embedding a form subclassed from 
        // sfGuardUser. It was vastly more work in every instance.
        // You have to clobber all of the other fields (you can 
        // automate that, but still). If you use embedForm you realize 
        // you've got a nested form that looks like a
        // nested form and an end user looking at that and
        // saying "why?" If you use mergeForm you can't save(). And if
        // you output the forms consecutively you have to manage your
        // own transactions. Adding two fields to the profile form
        // is definitely simpler.

        $this->widgetSchema['password2'] = new sfWidgetFormInputPassword(array(), array('maxlength' => 128));
        $this->widgetSchema['email2']    = new sfWidgetFormInputText();
        $this->widgetSchema['password']    = new sfWidgetFormInputPassword(array(),array('maxlength' => 128, 'required' => true));
        
        
        $this->widgetSchema->moveField('username', sfWidgetFormSchema::FIRST);
        $this->widgetSchema->moveField('first_name', sfWidgetFormSchema::AFTER, 'username');
        $this->widgetSchema->moveField('last_name', sfWidgetFormSchema::AFTER, 'first_name');
        $this->widgetSchema->moveField('password', sfWidgetFormSchema::AFTER, 'last_name');
        $this->widgetSchema->moveField('password2', sfWidgetFormSchema::AFTER, 'password');
        $this->widgetSchema->moveField('email_address', sfWidgetFormSchema::AFTER, 'password2');
        $this->widgetSchema->moveField('email2', sfWidgetFormSchema::AFTER, 'email_address');
        
        $this->widgetSchema->setLabels(array('password2' => 'Confirm Password'));
        $this->widgetSchema->setLabels(array(
            'email2' => 'Confirm Email'
        ));

        $this->widgetSchema->setNameFormat('sfApplyApply[%s]');
        //$this->widgetSchema->setFormFormatterName('list');

        

        // Passwords are never printed - ever - except in the context of Symfony form validation which has built-in escaping.
        // So we don't need a regex here to limit what is allowed
        // Don't print passwords when complaining about inadequate length
        $this->setValidator('password2', new sfValidatorString(array(
                    'required' => true,
                    'trim' => true,
                    'min_length' => 6,
                    'max_length' => 128
                        ), array(
                    'min_length' => 'That password is too short. It must contain a minimum of %min_length% characters.')));

        // Be aware that sfValidatorEmail doesn't guarantee a string that is preescaped for HTML purposes.
        // If you choose to echo the user's email address somewhere, make sure you escape entities.
        // <, > and & are rare but not forbidden due to the "quoted string in the local part" form of email address
        // (read the RFC if you don't believe me...).

        

        $this->setValidator('email2', new sfValidatorEmail(array(
                    'required' => true,
                    'trim' => true
                )));
        
        $this->getValidator('email_address')->setMessage('invalid', 'The email address is not a valid address or an account with that email address already exists. If you have forgotten your password, click "cancel", then "Reset My Password."');



        $schema = $this->validatorSchema;

        // Hey Fabien, adding more postvalidators is kinda verbose!
        $postValidator = $schema->getPostValidator();

        $postValidators = array(
            new sfValidatorSchemaCompare(
                    'password',
                    sfValidatorSchemaCompare::EQUAL,
                    'password2',
                    array(),
                    array('invalid' => 'The passwords did not match.')
            ),
            new sfValidatorSchemaCompare(
                    'email_address',
                    sfValidatorSchemaCompare::EQUAL,
                    'email2',
                    array(),
                    array('invalid' => 'The email addresses did not match.')
            )
        );

        if ($postValidator) {
            $postValidators[] = $postValidator;
        }

        $this->validatorSchema->setPostValidator(new sfValidatorAnd($postValidators));
    }

    public function setValidate($validate) {
        $this->validate = $validate;
    }


    public function updateObject($values = null) {
        $object = parent::updateObject($values);
        //$object->setUserId($this->userId);
        $object->setValidate($this->validate);
        $object->setIsActive(false);

        // Don't break subclasses!
        return $object;
    }

}

