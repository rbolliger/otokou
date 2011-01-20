<?php

require_once(sfConfig::get('sf_plugins_dir') . '/sfDoctrineApplyPlugin/modules/sfApply/lib/BasesfApplyActions.class.php');

require_once(dirname(__FILE__) . '/../lib/form/sfApplyApplyForm.class.php');

class sfApplyActions extends BasesfApplyActions {

    protected function mail($options) {
        $required = array('subject', 'parameters', 'email', 'fullname', 'html', 'text');
        foreach ($required as $option) {
            if (!isset($options[$option])) {
                throw new sfException("Required option $option not supplied to sfApply::mail");
            }
        }
        $message = $this->getMailer()->compose();
        $message->setSubject($options['subject']);

        // Render message parts
        $message->setBody($this->getPartial($options['html'], $options['parameters']), 'text/html');
        $message->addPart($this->getPartial($options['text'], $options['parameters']), 'text/plain');
        $address = $this->getFromAddress();
        $message->setFrom(array($address['email'] => $address['fullname']));
        $message->setTo(array($options['email'] => $options['fullname']));
        $this->getMailer()->send($message);
    }

    public function executeApply(sfRequest $request) {
        $this->form = $this->newForm('sfApplyApplyForm');
        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter('sfApplyApply'));
            if ($this->form->isValid()) {
                $guid = "n" . self::createGuid();
                $this->form->setValidate($guid);
                $this->form->save();
                try {
                    $profile = $this->form->getObject();
                    $this->sendVerificationMail($profile);
                    return 'After';
                } catch (Exception $e) {
                    $user = $this->form->getObject();
                    $user->delete();
                    throw $e;
                    // You could re-throw $e here if you want to 
                    // make it available for debugging purposes
                    return 'MailerError';
                }
            }
        }
    }

    static protected function createGuid() {
        $guid = "";
        // This was 16 before, which produced a string twice as
        // long as desired. I could change the schema instead
        // to accommodate a validation code twice as big, but
        // that is completely unnecessary and would break 
        // the code of anyone upgrading from the 1.0 version.
        // Ridiculously unpasteable validation URLs are a 
        // pet peeve of mine anyway.
        for ($i = 0; ($i < 8); $i++) {
            $guid .= sprintf("%02x", mt_rand(0, 255));
        }
        return $guid;
    }

    // apply uses this. Password reset also uses it in the case of a user who
    // was never verified to begin with

    protected function sendVerificationMail($profile) {
        $this->mail(array('subject' => sfConfig::get('app_sfApplyPlugin_apply_subject', sfContext::getInstance()->getI18N()->__("Please verify your account on %1%", array('%1%' => $this->getRequest()->getHost()))),
            'fullname' => $profile->getFullname(),
            'email' => $profile->getEmailAddress(),
            'parameters' => array('fullname' => $profile->getFullname(), 'validate' => $profile->getValidate()),
            'text' => 'sfApply/sendValidateNewText',
            'html' => 'sfApply/sendValidateNew'));
    }

    public function executeConfirm(sfRequest $request) {
        $validate = $this->request->getParameter('validate');
        // 0.6.3: oops, this was in sfGuardUserProfilePeer in my application
        // and therefore never got shipped with the plugin until I built
        // a second site and spotted it!
        // Note that this only works if you set foreignAlias and
        // foreignType correctly 
        $sfGuardUser = Doctrine_Query::create()->
                        from("sfGuardUser u")->
                        where("u.validate = ?", $validate)->
                        fetchOne();
        if (!$sfGuardUser) {
            return 'Invalid';
        }
        $type = self::getValidationType($validate);
        if (!strlen($validate)) {
            return 'Invalid';
        }
        $sfGuardUser->setValidate(null);
        $sfGuardUser->save();
        if ($type == 'New') {
            $sfGuardUser->setIsActive(true);
            $sfGuardUser->save();
            $this->getUser()->signIn($sfGuardUser);
        }
        if ($type == 'Reset') {
            $this->getUser()->setAttribute('sfApplyReset', $sfGuardUser->getId());
            return $this->redirect('sfApply/reset');
        }
    }

    static protected function getValidationType($validate) {
        $t = substr($validate, 0, 1);
        if ($t == 'n') {
            return 'New';
        } elseif ($t == 'r') {
            return 'Reset';
        } else {
            return sfView::NONE;
        }
    }

    public function executeResetRequest(sfRequest $request) {
        $user = $this->getUser();

        if ($user->isAuthenticated()) {
            $guardUser = $this->getUser()->getGuardUser();
            $this->forward404Unless($guardUser);
            return $this->resetRequestBody($guardUser);
        } else {
            $this->form = $this->newForm('sfApplyResetRequestForm');
            if ($request->isMethod('post')) {
                $this->form->bind($request->getParameter('sfApplyResetRequest'));
                if ($this->form->isValid()) {
                    // The form matches unverified users, but retrieveByUsername does not, so
                    // use an explicit query. We'll special-case the unverified users in
                    // resetRequestBody

                    $username_or_email = $this->form->getValue('username_or_email');
                    if (strpos($username_or_email, '@') !== false) {
                        $user = Doctrine::getTable('sfGuardUser')->createQuery('u')->where('u.email_address = ?', $username_or_email)->fetchOne();
                    } else {
                        $user = Doctrine::getTable('sfGuardUser')->createQuery('u')->where('username = ?', $username_or_email)->fetchOne();
                    }
                    return $this->resetRequestBody($user);
                }
            }
        }
    }
    
    public function resetRequestBody($user)
  {
    if (!$user)
    {
      return 'NoSuchUser';
    }
    $this->forward404Unless($user);
          

    if (!$user->getIsActive())
    {
      $type = $this->getValidationType($user->getValidate());
      if ($type === 'New')
      {
        try 
        {
          $this->sendVerificationMail($user);
        }
        catch (Exception $e)
        {
          return 'UnverifiedMailerError';
        }
        return 'Unverified';
      }
      elseif ($type === 'Reset')
      {
        // They lost their first password reset email. That's OK. let them try again
      }
      else
      {
        return 'Locked';
      }
    }
    $user->setValidate('r' . self::createGuid());
    $user->save();
    try
    {
      $this->mail(array('subject' => sfConfig::get('app_sfApplyPlugin_reset_subject',
          sfContext::getInstance()->getI18N()->__("Please verify your password reset request on %1%", array('%1%' => $this->getRequest()->getHost()))),
        'fullname' => $user->getFullname(),
        'email' => $user->getEmailAddress(),
        'parameters' => array('fullname' => $user->getFullname(), 'validate' => $user->getValidate(), 'username' => $user->getUsername()),
        'text' => 'sfApply/sendValidateResetText',
        'html' => 'sfApply/sendValidateReset'));
    } catch (Exception $e)
    {
      return 'MailerError';
    }
    return 'After';
  }

}
