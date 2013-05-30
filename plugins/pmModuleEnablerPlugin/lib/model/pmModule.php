<?php

class pmModule extends BasepmModule
{
  public function __toString()
  {
    $modules_names = sfConfig::get('app_pm_module_enabler_modules_names', array());

    return array_key_exists($this->getName(), $modules_names)?$modules_names[$this->getName()]:$this->getName();
  }
}
