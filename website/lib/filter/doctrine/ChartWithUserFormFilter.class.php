<?php

/**
 * Chart filter form.
 *
 * @package    otokou
 * @subpackage filter
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ChartWithUserFormFilter extends ChartFormFilter {

    public function configure() {

        parent::configure();

        unset($this['user_id']);

        $q = Doctrine_Core::getTable('Vehicle')
                ->createQuery('v')
                ->where('v.user_id = ?', $this->getUserId());

        $this->widgetSchema['vehicles_list']->setOption('query', $q);
        $this->validatorSchema['vehicles_list']->setOption('query', $q);



        


    }

    protected function getUserId() {

        return sfContext::getInstance()->getUser()->getGuarduser()->getId();
    }

}
