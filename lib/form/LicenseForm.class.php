<?php

/**
 * License form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class LicenseForm extends BaseLicenseForm
{
  public function configure()
  {
    unset($this['is_active']);
    $this->setWidget('person_id', new sfWidgetFormInputHidden());    

    $this->setWidget('date_from', new csWidgetFormDateInput());
    $this->setValidator('date_from', new mtValidatorDateString());

    $this->setWidget('date_to', new csWidgetFormDateInput());
    $this->setValidator('date_to', new mtValidatorDateString());
  }
}
