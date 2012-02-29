<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sfWidgetFormSchemaFormatterOtokou
 *
 * @author Raffaele Bolliger <raffaele.bolliger at gmail.com>
 */
class sfWidgetFormSchemaFormatterOtokou extends sfWidgetFormSchemaFormatter {

    protected
            $rowFormat = "<div class='sf_admin_form_row%row_class%'>\n  %error%\n <div>%label%\n  <div class='content'>%field%</div> \n%help% </div>\n%hidden_fields%</div>\n",
            $errorRowFormat = "<li>\n%errors%</li>\n",
            $helpFormat = '<div class="help">%help%</div>',
            $decoratorFormat = "<ul>\n  %content%</ul>";

    
    
    public function formatRow($label, $field, $errors = array(), $help = '', $hiddenFields = null)
  {
    $row = parent::formatRow(
      $label,
      $field,
      $errors,
      $help,
      $hiddenFields
    );
    
    $errors = (count($errors) > 0) ? ' errors' : '';
 
    return strtr($row, array(
      '%row_class%' => $errors,
    ));
  }
    
    
}