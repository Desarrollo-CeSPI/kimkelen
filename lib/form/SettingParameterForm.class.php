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
      unset($this['name']);
      $this->setWidget('description', new sfWidgetFormReadOnly(array(
      'plain'          => false,
      'value_callback' => array('SettingParameterPeer', 'retrieveByPK')
    )));
  }
}
