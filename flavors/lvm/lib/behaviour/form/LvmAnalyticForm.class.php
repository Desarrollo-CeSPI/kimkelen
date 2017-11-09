<?php

/**
 * Analytic form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Your name here
 */
class LvmAnalyticForm extends AnalyticForm
{
  public function configure()
  {
      parent::configure();
      $this->unsetFields();
  }
  
  public function unsetFields()
  {
    unset(
      $this['description'],
      $this['id'],
      $this['career_student_id'],
      $this['certificate'],
      $this['created_at']
         
    );
  }
}
