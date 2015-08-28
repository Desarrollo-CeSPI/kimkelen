<?php

/**
 * Holiday form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Your name here
 */
class HolidayForm extends BaseHolidayForm
{
  public function configure()
  {
    $this->setWidget('day', new csWidgetFormDateInput());
    $this->setValidator('day', new mtValidatorDateString(array('required' => true)));
  }
}