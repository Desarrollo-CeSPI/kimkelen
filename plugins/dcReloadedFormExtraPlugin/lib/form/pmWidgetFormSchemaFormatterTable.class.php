<?php

class pmWidgetFormSchemaFormatterTable extends sfWidgetFormSchemaFormatterTable
{
  protected $form = null;
  
  public function __construct(sfForm $form)
  {
    parent::__construct($form->getWidgetSchema());
    $this->setForm($form);
  }

  public function setForm($form)
  {
    $this->form = $form;
  }

  public function getForm()
  {
    return $this->form;
  }

  public function generateLabel($name, $attributes = array())
  {
    $validatorSchema = $this->form->getValidatorSchema();
    
    $class = (isset($validatorSchema[$name]) && $validatorSchema[$name]->getOption("required")) ? "required" : false;
    
    if ($class)
    {
      $attributes["class"] = isset($attributes["class"]) ? $attributes["class"]." $class" : $class;
    }
    
    return parent::generateLabel($name, $attributes);
  }
}