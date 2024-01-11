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
      $this->widgetSchema['dipregep_number']= new sfWidgetFormInput();
      $this->widgetSchema->setLabel("dipregep_number",' ');
      $this->setWidget('observations', new sfWidgetFormInput());
      $this->validatorSchema->setOption("allow_extra_fields", true);
      $this->unsetFields();
  }
  
  public function unsetFields()
  {
    unset(
      $this['description'],
      $this['id'],
      $this['career_student_id'],
      $this['certificate'],
      $this['previous_certificate'],
      $this['created_at']  
         
    );
  }
}
