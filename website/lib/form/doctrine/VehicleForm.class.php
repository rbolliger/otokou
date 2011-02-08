<?php

/**
 * Vehicle form.
 *
 * @package    otokou
 * @subpackage form
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class VehicleForm extends BaseVehicleForm
{
  public function configure()
  {
      
      unset(
        $this['created_at'],
        $this['updated_at'],
        $this['slug']              
              );
      
  }
}
