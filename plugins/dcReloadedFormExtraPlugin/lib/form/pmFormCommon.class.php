<?php

/**
 * @author Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
abstract class pmFormCommon
{
  public static function setup(sfForm $form)
  {
    $formatter = new pmWidgetFormSchemaFormatterTable($form);
    $form->getWidgetSchema()->addFormFormatter("pm_table", $formatter);
    $form->getWidgetSchema()->setFormFormatterName("pm_table");
    
    $form->unsetFields();
    
    // auto configure widgets
    pmWidgetFactory::replaceWidgets($form);
    
    // auto configure validators
    pmValidatorFactory::replaceValidators($form);
    
    $form->configureWidgets();
    $form->configureValidators();
    
    if ($form instanceof pmFormPropel)
    {
      $sf_user = sfContext::getInstance()->getUser();
      if (method_exists($sf_user, "getGuardUser"))
      {
        $user_id = $sf_user->getGuardUser()->getId();
        
        if (array_key_exists("created_by", $form->getWidgetSchema()->getFields()) && $form->getObject()->isNew())
        {
          $form->getObject()->setCreatedBy($user_id);
        }
        
        if (array_key_exists("updated_by", $form->getWidgetSchema()->getFields()))
        {
          $form->getObject()->setUpdatedBy($user_id);
        }
      }
    }
  }
}