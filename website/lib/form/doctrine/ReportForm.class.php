<?php

/**
 * Report form.
 *
 * @package    otokou
 * @subpackage form
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ReportForm extends BaseReportForm {

    public function configure() {

        unset(
                $this['created_at'],
                $this['updated_at'],
                $this['sha'],
                $this['is_new'],
                $this['slug'],
                $this['num_vehicles']
        );

        $this->validatorSchema['name'] = new sfValidatorString(array('max_length' => 255, 'required' => true));
        $this->widgetSchema->moveField('name', sfWidgetFormSchema::FIRST);

        $this->widgetSchema['vehicles_list']->setOptions(
                array_merge($this->widgetSchema['vehicles_list']->getOptions(),
                        array(
                            'expanded' => true,
                            'multiple' => true,
                            'label' => 'Vehicles',
                            'add_empty' => false,
                )));

        $this->validatorSchema['vehicles_list']->setOptions(
                array_merge($this->validatorSchema['vehicles_list']->getOptions(),
                        array(
                            'required' => true,
                            'multiple' => true
                )));


        $this->widgetSchema->moveField('vehicles_list', sfWidgetFormSchema::AFTER, 'name');

// Dates range
        unset(
                $this['date_from'],
                $this['date_to']
        );

        $years = range(date('Y') - 25, date('Y') + 5);

        $widget = new sfWidgetFormDate(array(
                    'format' => '%day%/%month%/%year%',
                    'years' => array_combine($years, $years),
                ));

        $this->widgetSchema['date_range'] = new sfWidgetFormFilterDate(array(
                    'from_date' => new sfWidgetFormJQueryDate(array(
                        'config' => '{changeMonth: true, changeYear: true, yearRange: \'-25:+5\'}',
                        'image' => '/images/calendar.png',
                        'date_widget' => $widget
                    )),
                    'to_date' => new sfWidgetFormJQueryDate(array(
                        'config' => '{changeMonth: true, changeYear: true, yearRange: \'-25:+5\'}',
                        'image' => '/images/calendar.png',
                        'date_widget' => $widget
                    )),
                    'with_empty' => false,
                    'template' => 'from %from_date% to %to_date%',
                ))
        ;

        $this->validatorSchema['date_range'] = new sfValidatorDateRange(
                        array(
                            'required' => false,
                            'from_date' => new sfValidatorDate(array('required' => false)),
                            'to_date' => new sfValidatorDate(array('required' => false))
                ));


// Kilometers range
        unset(
                $this['kilometers_from'],
                $this['kilometers_to']
        );

        $this->widgetSchema['kilometers_range'] = new sfWidgetFormFilterDate(array(
                    'from_date' => new sfWidgetFormInput(array('default' => null)),
                    'to_date' => new sfWidgetFormInput(array('default' => null)),
                    'with_empty' => false,
                    'template' => 'between %from_date% and %to_date%',
                ));

        $this->validatorSchema['kilometers_range'] = new sfValidatorDateRange(array(
                    'required' => false,
                    'from_date' => new sfValidatorNumber(array('required' => false)),
                    'to_date' => new sfValidatorNumber(array('required' => false))
                ));


        $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkDateAndKilometersFrom'))));
        $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkDateAndKilometersTo'))));
    }

    public function checkDateAndKilometersFrom($validator, $values) {

        if (isset($values['date_range']['from']) && isset($values['kilometers_range']['from'])) {
            $error = new sfValidatorError($validator, 'Only one field between "date from" and "kilometers from" can be defined.');

            throw new sfValidatorErrorSchema($validator, array('date_range' => $error, 'kilometers_range' => $error));
        }

        return $values;
    }

    public function checkDateAndKilometersTo($validator, $values) {

        if (isset($values['date_range']['to']) && isset($values['kilometers_range']['to'])) {
            $error = new sfValidatorError($validator, 'Only one field between "date to" and "kilometers to" can be defined.');

            throw new sfValidatorErrorSchema($validator, array('date_range' => $error, 'kilometers_range' => $error));
        }

        return $values;
    }

    public function processValues($values) {

        $values = parent::processValues($values);

        // converting ranges to DB fields
       if (isset($values['date_range']['from'])) {
           $values['date_from'] = $values['date_range']['from'];
       }

       if (isset($values['date_range']['to'])) {
           $values['date_to'] = $values['date_range']['to'];
       }

       unset($values['date_range']);

       if (isset($values['kilometers_range']['from'])) {
           $values['kilometers_from'] = $values['kilometers_range']['from'];
       }

       if (isset($values['kilometers_range']['to'])) {
           $values['kilometers_to'] = $values['kilometers_range']['to'];
       }

       unset($values['kilometers_range']);

       // setting default values to range fields
       if (!isset($values['date_from']) && !isset($values['kilometers_from'])) {
           $values['kilometers_from'] = 0; // vehicle origin
       }

       if (!isset($values['date_to']) && !isset($values['kilometers_to'])) {
           $values['date_to'] = date('Y-m-d'); // today
       }

       return $values;
    }

}
