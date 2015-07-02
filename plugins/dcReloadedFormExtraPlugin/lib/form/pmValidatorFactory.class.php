<?php

/**
 * @author Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class pmValidatorFactory
{
  public static function replaceValidators(sfForm $form)
  {
    foreach ($form->getWidgetSchema()->getFields() as $name => $widget)
    {
      if ($widget instanceof mtWidgetFormInputDate)
      {
        $form->setValidator($name, self::getDateValidator());
      }
      elseif ($widget instanceof sfWidgetFormFilterDate)
      {
        $form->getValidator($name)->setOption("from_date", self::getDateValidator(array("required" => false)));
        $form->getValidator($name)->setOption("to_date", self::getDateValidator(array("required" => false)));
      }
      
      if ($name == "attachment")
      {
        $form->setValidator($name, new sfValidatorFile(array(
          "required" => false,
          "path" => sfConfig::get("sf_upload_dir")
        )));
      }
      elseif ($name == "email")
      {
        $form->setValidator($name, new sfValidatorEmail());
      }
    }
  }
  
  public static function getDateValidator($options = array())
  {
    return new mtValidatorDateString($options);
  }
}