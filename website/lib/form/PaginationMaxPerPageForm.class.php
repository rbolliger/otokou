<?php

class PaginationMaxPerPageForm extends sfForm
{
 protected
   $user = null;

 /**
  * Constructor.
  *
  * @param sfUser A sfUser instance
  * @param array  An array of options
  * @param string A CSRF secret (false to disable CSRF protection, null to use the global CSRF secret)
  *
  * @see sfForm
  */
 public function __construct(sfUser $user, $options = array(), $CSRFSecret = null)
 {
   $this->user = $user;

   parent::__construct(array(), $options, $CSRFSecret);
   
   // the defaults depend on the options, so set them after construction
   $this->setDefaults(array(
       'max_per_page' => $user->getAttribute(self::getMaxPerPageName(), self::getMaxPerPageValue()),
       ));
 }

 /**
  * Changes the current max_per_page attribute.
  */
 public function save()
 {
   $this->user->setAttribute(self::getMaxPerPageName(), $this->getValue('max_per_page'));
 }

 /**
  * Processes the current request.
  *
  * @param  sfRequest A sfRequest instance
  *
  * @return Boolean   true if the form is valid, false otherwise
  */
 public function process(sfRequest $request)
 {
   $data = array('max_per_page' => $request->getParameter('max_per_page', $this->user->getAttribute(self::getMaxPerPageName(), $this->getDefault('max_per_page'))));
   if ($request->hasParameter(self::$CSRFFieldName))
   {
     $data[self::$CSRFFieldName] = $request->getParameter(self::$CSRFFieldName);
   }

   $this->bind($data);

   if ($isValid = $this->isValid())
   {
     $this->save();
   }

   return $isValid;
 }

 public function configure()
 {
   $this->setWidgets(array(
        'max_per_page' => new sfWidgetFormSelect(array(
        'choices' => self::getMaxPerPageChoices(),
        'default' => $this->getDefault('max_per_page'),
     )),
   ));

   $this->setValidators(array(
     'max_per_page' => new sfValidatorChoice(array(
       'choices' => array_keys(self::getMaxPerPageChoices()),
       'required' => false,
     )),
   ));
 }

 protected function getMaxPerPageChoices()
 {
   if (
     isset($this->options['max_per_page_choices'])
     &&
     is_array($this->options['max_per_page_choices'])
     &&
     !empty($this->options['max_per_page_choices'])
   )
   {
     $default = $this->options['max_per_page_choices'];
   } else {

   $default = array(
     5,
     10,
     25,
     50,
   );
   
   }
   
   $def = array($this->getOption('default') ? $this->getOption('default') : null);

   
   $values = array_unique(array_merge($default,$def));
   
   sort($values);
   
   $values = array_combine(array_values($values),array_values($values));

   return $values;
 }

 protected function getMaxPerPageName()
 {
   if (
     isset($this->options['max_per_page_name'])
     &&
     is_string($this->options['max_per_page_name'])
     &&
     !empty($this->options['max_per_page_name'])
   )
   {
     return $this->options['max_per_page_name'];
   }

   return 'pager_max_per_page';
 }

 protected function getMaxPerPageValue()
 {
   if (
     isset($this->options['max_per_page_value'])
     &&
     is_numeric($this->options['max_per_page_value'])
     &&
     !empty($this->options['max_per_page_value'])
   )
   {
     return intval($this->options['max_per_page_value']);
   }

   return 10;
 }
 

}
