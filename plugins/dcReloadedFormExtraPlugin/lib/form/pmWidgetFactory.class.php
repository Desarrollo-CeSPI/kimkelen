<?php

/**
 * @author Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class pmWidgetFactory
{
  public static function replaceWidgets(sfForm $form)
  {
    foreach ($form->getWidgetSchema()->getFields() as $name => $widget)
    {
      if ($widget instanceof sfWidgetFormDate)
      {
        $form->setWidget($name, self::getDateWidget());
      }
      elseif ($widget instanceof sfWidgetFormFilterDate)
      {
        $form->getWidget($name)->setOption("from_date", self::getDateWidget(array("use_own_help" => false)));
        $form->getWidget($name)->setOption("to_date", self::getDateWidget(array("use_own_help" => false)));
        $form->getWidget($name)->setOption("template", __("from %from_date% to %to_date%"));
      }
      elseif ($widget instanceof sfWidgetFormTextarea)
      {
        $form->getWidget($name)->setAttribute("rows", 15);
        $form->getWidget($name)->setAttribute("cols", 100);
      }
      
      if ($name == "attachment")
      {
        $form->setWidget($name, new sfWidgetFormInputFile());
      }
      elseif ($name == "created_by" || $name == "updated_by")
      {
        if ($form instanceof sfFormPropel)
        {
          $form->setWidget($name, new sfWidgetFormInputHidden());
        }
      }
      elseif ($name == "password")
      {
        $form->setWidget($name, new sfWidgetFormInputPassword());
      }
    }
  }
  
  public static function getDateWidget($options = array())
  {
    return new mtWidgetFormInputDate($options);
  }
}