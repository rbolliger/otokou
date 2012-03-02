<?php

/**
 * Charge filter form.
 *
 * @package    otokou
 * @subpackage filter
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ChargeFormFilter extends BaseChargeFormFilter {

    public function configure() {
        parent::configure();


        $this->widgetSchema['category_id']->setOption('expanded', true);
        $this->widgetSchema['category_id']->setOption('multiple', true);
        $this->widgetSchema['category_id']->setOption('add_empty', false);
        $this->widgetSchema['category_id']->setOption('label', 'Categories');

        $this->validatorSchema['category_id']->setOption('multiple', true);


        $widget = new sfWidgetFormDate(array(
                    'format' => '%day%-%month%-%year%',
                ));

        $this->widgetSchema['date'] = new sfWidgetFormFilterDate(array(
                    'from_date' => new sfWidgetFormJQueryDate(array(
                        'config' => '{}',
                        'image' => '/images/icons/calendar.png',
                        'date_widget' => $widget
                    )),
                    'to_date' => new sfWidgetFormJQueryDate(array(
                        'config' => '{}',
                        'image' => '/images/icons/calendar.png',
                        'date_widget' => $widget
                    )),
                    'with_empty' => false,
                    'template' => '<span>from %from_date% to %to_date%</span>',
                ))
        ;
        
        $this->validatorSchema['date'] = new sfValidatorDateRange(
                        array(
                            'required' => false,
                            'from_date' => new sfValidatorDate(array('required' => false)),
                            'to_date' => new sfValidatorDate(array('required' => false))
                ));


        $this->widgetSchema['kilometers'] = new sfWidgetFormFilterDate(array(
                    'from_date' => new sfWidgetFormInput(array('default' => null)),
                    'to_date' => new sfWidgetFormInput(array('default' => null)),
                    'with_empty' => false,
                    'template' => '<span> %from_date% to %to_date% </span>',
                ));

        $this->validatorSchema['kilometers'] = new sfValidatorDateRange(array(
                    'required' => false,
                    'from_date' => new sfValidatorNumber(array('required' => false)),
                    'to_date' => new sfValidatorNumber(array('required' => false))
                ));


        $this->widgetSchema['amount'] = clone $this->widgetSchema['kilometers'];
        $this->validatorSchema['amount'] = clone $this->validatorSchema['kilometers'];

        $this->widgetSchema['quantity'] = clone $this->widgetSchema['kilometers'];
        $this->validatorSchema['quantity'] = clone $this->validatorSchema['kilometers'];
    }

    public function addKilometersColumnQuery(Doctrine_Query $query, $field, $values) {
        $this->addRangeQuery($query, $field, $values);
    }

    public function addAmountColumnQuery(Doctrine_Query $query, $field, $values) {

        $this->addRangeQuery($query, $field, $values);
    }

    public function addQuantityColumnQuery(Doctrine_Query $query, $field, $values) {

        $this->addRangeQuery($query, $field, $values);
    }

    public function addRangeQuery(Doctrine_Query $query, $field, $values) {

        $fieldName = $this->getFieldName($field);

        if ($values['from']) {
            $query->andWhere(sprintf('%s.%s >= ?', $query->getRootAlias(), $fieldName), $values['from']);
        }

        if ($values['to']) {
            $query->andWhere(sprintf('%s.%s <= ?', $query->getRootAlias(), $fieldName), $values['to']);
        }
    }

}
