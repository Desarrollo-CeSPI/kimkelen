<?php
class AttendanceJustificationFormFilter extends sfForm
{
  public function  configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");

    $this->setWidgets(array(
      'from_date' => new csWidgetFormDateInput(),
      'to_date' => new csWidgetFormDateInput(),
      'student' => new sfWidgetFormInput(),
      'attendance_subject'=> new sfWidgetFormInputCheckbox()
    ));
    
    $this->setValidators(array(
      'from_date' => new mtValidatorDateString(array('required' => false)),
      'to_date' => new mtValidatorDateString(array('required' => false)),
      'student' => new sfValidatorString(array('required' => false)),
      'attendance_subject'=>  new sfValidatorBoolean()
    ));

    $this->mergePostValidator(new sfValidatorCallback(array(
      'callback' => array($this, 'globalValidation')
    ), array(
      'required' => 'You must specify at least one filtering criteria.'
    )));

    $this->getWidgetSchema()->setNameFormat('attendance_justification[%s]');
  }

  public function globalValidation(sfValidatorBase $validator, $values)
  {
    $valid = false;

    foreach ($values as $value)
    {
      $valid = $valid || (null !== $value && '' != $value);
    }

    if (!$valid)
    {
      throw new sfValidatorError($validator, 'required');
    }

    return $values;
  }



}
