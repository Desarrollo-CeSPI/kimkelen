<?php

/**
 * Analytic form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Your name here
 */
class NacionalAnalyticForm extends AnalyticForm
{
  public function configure()
  {
      parent::configure();
      $this->unsetFields();
      $this->setWidget('observations', new sfWidgetFormInput());
      $this->setWidget('previous_certificate', new sfWidgetFormInput());  
}
  
  public function unsetFields()
  {
    unset(
      $this['description'],
      $this['id'],
      $this['career_student_id'],
      $this['certificate'],
      $this['created_at'],
      $this['certificate_number']
         
    );
  }
}
