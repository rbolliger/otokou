<?php

require_once(sfConfig::get('sf_plugins_dir') . '/sfDoctrineApplyPlugin/modules/sfApply/lib/BasesfApplyActions.class.php');

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

}
