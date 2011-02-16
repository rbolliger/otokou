<?php

/**
 * Graph filter form.
 *
 * @package    otokou
 * @subpackage filter
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class GraphFormFilter extends BaseGraphFormFilter {

    public function configure() {

        // Removing some fields
        unset(
                $this['sha'],
                $this['graph_name'],
                $this['created_at'],
                $this['updated_at']
        );

        // Vehicle
        $this->widgetSchema['vehicle_id']->setOption('expanded', true);
        $this->widgetSchema['vehicle_id']->setOption('multiple', true);
        $this->widgetSchema['vehicle_id']->setOption('label', 'Vehicles');
        $this->widgetSchema['vehicle_id']->setOption('add_empty', false);

        $this->validatorSchema['vehicle_id']->setOption('required', false);
        $this->validatorSchema['vehicle_id']->setOption('multiple', true);


        // Category
        $this->widgetSchema['category_id']->setOption('expanded', true);
        $this->widgetSchema['category_id']->setOption('multiple', true);
        $this->widgetSchema['category_id']->setOption('add_empty', false);
        $this->widgetSchema['category_id']->setOption('label', 'Categories');

        $this->validatorSchema['category_id']->setOption('multiple', true);

        // Display options
        $display_choices = Doctrine_Core::getTable('Graph')->getDisplayChoices();

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


        $this->widgetSchema->moveField('vehicle_display', sfWidgetFormSchema::BEFORE, 'category_id');

        // Category Display
        $this->widgetSchema['category_display'] = clone $this->widgetSchema['vehicle_display'];
        $this->widgetSchema['category_display']->setLabel('Categories aggregation');

        $this->validatorSchema['category_display'] = clone $this->validatorSchema['vehicle_display'];

        $this->widgetSchema->moveField('category_display', sfWidgetFormSchema::AFTER, 'category_id');

        // Dates range
        unset(
                $this['date_from'],
                $this['date_to']
        );

        $widget = new sfWidgetFormDate(array(
                    'format' => '%day%/%month%/%year%',
                ));

        $this->widgetSchema['date_range'] = new sfWidgetFormFilterDate(array(
                    'from_date' => new sfWidgetFormJQueryDate(array(
                        'config' => '{}',
                        'image' => '/images/calendar.png',
                        'date_widget' => $widget
                    )),
                    'to_date' => new sfWidgetFormJQueryDate(array(
                        'config' => '{}',
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


        // Display options
        $range_types = Doctrine_Core::getTable('Graph')->getRangeTypes();

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



        $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkDateAndKilometersRanges'))));
    }

    public function checkDateAndKilometersRanges($validator, $values) {

        if (($values['date_range']['from'] || $values['date_range']['to']) && ($values['kilometers_range']['from'] || $values['kilometers_range']['to'])) {
            $error = new sfValidatorError($validator, "Only one field between date and kilometers range can be defined for X-axis.");

            // throw an error bound to the password field
            throw new sfValidatorErrorSchema($validator, array('date_range' => $error, 'kilometers_range' => $error));
        }

        // password is correct, return the clean values
        return $values;
    }

}
