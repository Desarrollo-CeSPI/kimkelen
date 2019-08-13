<?php

/**
 * SettingParameter form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Your name here
 */
class SettingParameterForm extends BaseSettingParameterForm
{
  public function configure()
  {
    $options = array(
      'min'      => 15,
      'max'      => 30,
      'required' => true
    );

    $messages = array(
      'min'     => 'El valor del parámetro debe ser al menos %min%.',
      'max'     => 'El valor del parámetro debe ser a lo sumo %max%.',
      'invalid' => 'El valor ingresado es inválido, solo se aceptan números enteros.'
    );
      unset($this['name']);
      
      $this->setWidget('description', new sfWidgetFormReadOnly(array(
      'plain'          => false,
      'value_callback' => array('SettingParameterPeer', 'retrieveByPK')
    )));
      
     $this->setValidator('value', new sfValidatorInteger($options, $messages));
      
  }
}
