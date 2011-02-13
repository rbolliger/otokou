<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ChargeForGraphsFilter
 *
 * @author ruf
 */
class ChargeForGraphsFilter extends ChargeFormWithUserFilter {
    
    protected $display_choices = array(
        'stacked' => 'Stacked',
        'single'  => 'Single'
        );

    public function configure() {

        parent::configure();
        
        $this->useFields(array('vehicle_id', 'category_id', 'date', 'kilometers'));
        
        $this->widgetSchema['vehicle_display'] = new sfWidgetFormChoice(
                array(
                    'choices'   => $this->display_choices,
                    'multiple'  => false,
                    'expanded'  => true,
                    'label'     => 'Vehicles aggregation',
                ));
        
        $this->validatorSchema['vehicle_display'] = new sfValidatorChoice(
                array(
                    'choices' => array_keys($this->display_choices),
                    'multiple' => false,
                    'required' => false,
                ));
        
        $this->widgetSchema->moveField('vehicle_display', sfWidgetFormSchema::BEFORE, 'category_id');
        
        $this->widgetSchema['category_display'] = clone $this->widgetSchema['vehicle_display'];
        $this->validatorSchema['category_display'] = clone $this->validatorSchema['vehicle_display'];
        $this->widgetSchema->moveField('category_display', sfWidgetFormSchema::BEFORE, 'date');
        
        $q = Doctrine_Core::getTable($this->getRelatedModelName('Vehicle'))
                        ->createQuery('v')
                        ->andWhere('v.user_id = ?', $this->getUserId());
        
               
        $this->widgetSchema['vehicle_id']->setOption('query', $q);
        $this->validatorSchema['vehicle_id']->setOption('query', $q);
        
    }
    
    public function addVehicleDisplayColumnQuery(Doctrine_Query $query, $field, $values) {
        
        /*
        if ('single' == $values) {
           $query->addGroupBy($query->getRootAlias().'.vehicle_id'); 
        }

*/
        
        
        
        
    }

}

?>
