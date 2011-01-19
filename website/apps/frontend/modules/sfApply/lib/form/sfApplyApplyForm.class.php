<?php

class sfApplyApplyForm extends sfGuardUserProfileForm {

    private $validate = null;

    public function configure() {
        parent::configure();

        // We're making a new user or editing the user who is
        // logged in. In neither case is it appropriate for
        // the user to get to pick an existing userid. The user
        // also doesn't get to modify the validate field which
        // is part of how their account is verified by email.

        unset($this['user_id'], $this['validate']);

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



        
        $this->setWidgets(array(
            'first_name' => new sfWidgetFormInputText(),
            'last_name'  => new sfWidgetFormInputText(),
            'username'   => new sfWidgetFormInput(array(), array('maxlength' => 16)),
            'password'   => new sfWidgetFormInputPassword(array(), array('maxlength' => 128)),
            'password2'  => new sfWidgetFormInputPassword(array(), array('maxlength' => 128)),
            'email'      => new sfWidgetFormInputText(),
            'email2'      => new sfWidgetFormInputText(),
                    ));

        $this->widgetSchema->moveField('username', sfWidgetFormSchema::FIRST);
        $this->widgetSchema->moveField('first_name', sfWidgetFormSchema::AFTER, 'username');
        $this->widgetSchema->moveField('last_name', sfWidgetFormSchema::AFTER, 'first_name');
        $this->widgetSchema->moveField('password', sfWidgetFormSchema::AFTER, 'last_name');
        $this->widgetSchema->moveField('password2', sfWidgetFormSchema::AFTER, 'password');
        $this->widgetSchema->moveField('email', sfWidgetFormSchema::AFTER, 'password2');
        $this->widgetSchema->moveField('email2', sfWidgetFormSchema::AFTER, 'email');
        
        $this->widgetSchema->setLabels(array('password2' => 'Confirm Password'));
        $this->widgetSchema->setLabels(array(
            'email2' => 'Confirm Email'
        ));

        $this->widgetSchema->setNameFormat('sfApplyApply[%s]');
        $this->widgetSchema->setFormFormatterName('list');


        $this->setValidators(array(
            'first_name' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'last_name' => new sfValidatorString(array('max_length' => 255, 'required' => false))
        ));

        // We have the right to an opinion on these fields because we
        // implement at least part of their behavior. Validators for the
        // rest of the user profile come from the schema and from the
        // developer's form subclass

        $this->setValidator('username', new sfValidatorAnd(array(
                    new sfValidatorString(array(
                        'required' => true,
                        'trim' => true,
                        'min_length' => 0,
                        'max_length' => 128
                    )),
                    // Usernames should be safe to output without escaping and generally username-like.
                    new sfValidatorRegex(array(
                        'pattern' => '/^\w+$/'
                            ), array('invalid' => 'Usernames must contain only letters, numbers and underscores.')),
                    new sfValidatorDoctrineUnique(array(
                        'model' => 'sfGuardUser',
                        'column' => 'username'
                            ), array('invalid' => 'There is already a user by that name. Choose another.'))
                ))
        );

        // Passwords are never printed - ever - except in the context of Symfony form validation which has built-in escaping.
        // So we don't need a regex here to limit what is allowed
        // Don't print passwords when complaining about inadequate length
        $this->setValidator('password', new sfValidatorString(array(
                    'required' => true,
                    'trim' => true,
                    'min_length' => 6,
                    'max_length' => 128
                        ), array(
                    'min_length' => 'That password is too short. It must contain a minimum of %min_length% characters.')));

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

        $this->setValidator('email', new sfValidatorAnd(array(
                    new sfValidatorEmail(array('required' => true, 'trim' => true)),
                    new sfValidatorString(array('required' => true, 'max_length' => 80)),
                    new sfValidatorDoctrineUnique(array(
                        'model' => 'sfGuardUser',
                        'column' => 'email_address'
                            ), array('invalid' => 'An account with that email address already exists. If you have forgotten your password, click "cancel", then "Reset My Password."'))
                )));

        $this->setValidator('email2', new sfValidatorEmail(array(
                    'required' => true,
                    'trim' => true
                )));



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
                    'email',
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

    public function doSave($con = null) {
        $user = new sfGuardUser();
        $user->setUsername($this->getValue('username'));
        $user->setPassword($this->getValue('password'));
        $user->setFirstName($this->getValue('first_name'));
        $user->setLastName($this->getValue('last_name'));
        $user->setEmailAddress($this->getValue('email'));
        // They must confirm their account first
        $user->setIsActive(false);
        $user->save();
        $this->userId = $user->getId();

        return parent::doSave($con);
    }

    public function updateObject($values = null) {
        $object = parent::updateObject($values);
        $object->setUserId($this->userId);
        $object->setValidate($this->validate);

        // Don't break subclasses!
        return $object;
    }

}

