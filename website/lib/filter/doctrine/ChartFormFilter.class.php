<?php

/**
 * Chart filter form.
 *
 * @package    otokou
 * @subpackage filter
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ChartFormFilter extends BaseChartFormFilter {

    public function configure() {

        // Removing some fields
        unset(
                $this['slug'],
                $this['format'],
                $this['created_at'],
                $this['updated_at'],
                $this['chart_name']
        );

        // Vehicle
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
                            'required' => false,
                            'multiple' => true
                )));


        $this->widgetSchema->moveField('vehicles_list', sfWidgetFormSchema::FIRST);


        // Category
        $this->widgetSchema['categories_list']->setOptions(
                array_merge($this->widgetSchema['categories_list']->getOptions(),
                        array(
                            'expanded' => true,
                            'multiple' => true,
                            'label' => 'Categories',
                            'add_empty' => false,
                )));

        $this->validatorSchema['categories_list']->setOptions(
                array_merge($this->validatorSchema['categories_list']->getOptions(),
                        array(
                            'required' => false,
                            'multiple' => true
                )));


        // Display options
        $display_choices = Doctrine_Core::getTable('Chart')->getDisplayChoices();

        // Vehicle display
        $this->widgetSchema['vehicle_display'] = new sfWidgetFormChoice(
                        array(
                            'choices' => $display_choices,
                            'multiple' => false,
                            'expanded' => true,
                            'label' => 'Vehicles aggregation',
                ));

        $this->validatorSchema['vehicle_display'] = new sfValidatorChoice(
                        array(
                            'choices' => array_keys($display_choices),
                            'multiple' => false,
                            'required' => false,
                ));


        $this->widgetSchema->moveField('vehicle_display', sfWidgetFormSchema::BEFORE, 'categories_list');

        // Category Display
        $this->widgetSchema['category_display'] = clone $this->widgetSchema['vehicle_display'];
        $this->widgetSchema['category_display']->setLabel('Categories aggregation');

        $this->validatorSchema['category_display'] = clone $this->validatorSchema['vehicle_display'];

        $this->widgetSchema->moveField('category_display', sfWidgetFormSchema::AFTER, 'categories_list');

        // Dates range
        unset(
                $this['date_from'],
                $this['date_to']
        );

        $years = range(date('Y') - 25, date('Y') + 5);

        $widget = new sfWidgetFormDate(array(
                    'format' => '%day%/%month%/%year%',
                    'years'  => array_combine($years, $years),
                ));

        $this->widgetSchema['date_range'] = new sfWidgetFormFilterDate(array(
                    'from_date' => new sfWidgetFormJQueryDate(array(
                        'config' => '{changeMonth: true, changeYear: true, yearRange: \'-25:+5\'}',
                        'image' => '/images/calendar.png',
                        'date_widget' => $widget,
                        'default' => null,
                    )),
                    'to_date' => new sfWidgetFormJQueryDate(array(
                        'config' => '{changeMonth: true, changeYear: true, yearRange: \'-25:+5\'}',
                        'image' => '/images/calendar.png',
                        'date_widget' => $widget,
                        'default' => null,
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
                    'to_date' => new sfValidatorNumber(array('required' => false)),                   
                ),
                array(
                    'invalid' => 'Please, define a valid range', 
                )
                );


        // Display options
        $range_types = Doctrine_Core::getTable('Chart')->getRangeTypes();

        // Vehicle display
        $this->widgetSchema['range_type'] = new sfWidgetFormChoice(
                        array(
                            'choices' => $range_types,
                            'multiple' => false,
                            'expanded' => true,
                            'label' => 'X axis type',
                ));

        $this->validatorSchema['range_type'] = new sfValidatorChoice(
                        array(
                            'choices' => array_keys($range_types),
                            'multiple' => false,
                            'required' => false,
                ));

        $this->widgetSchema->moveField('range_type', sfWidgetFormSchema::BEFORE, 'date_range');



        $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkDateAndKilometersFrom'))));
        $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkDateAndKilometersTo'))));
    }

    public function checkDateAndKilometersFrom($validator, $values) {

        if (isset($values['date_range']['from']) && isset($values['kilometers_range']['from'])) {
            $error = new sfValidatorError($validator, 'Only one field between "date from" and "kilometers from" can be defined for X-axis.');

            throw new sfValidatorErrorSchema($validator, array('date_range' => $error, 'kilometers_range' => $error));
        }

        return $values;
    }

    public function checkDateAndKilometersTo($validator, $values) {

        if (isset($values['date_range']['to']) && isset($values['kilometers_range']['to'])) {
            $error = new sfValidatorError($validator, 'Only one field between "date to" and "kilometers to" can be defined for X-axis.');

            throw new sfValidatorErrorSchema($validator, array('date_range' => $error, 'kilometers_range' => $error));
        }

        return $values;
    }

}
