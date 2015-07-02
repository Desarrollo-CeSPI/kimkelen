<?php

class pmConfiguration extends BasepmConfiguration
{
  protected static $instance = null;

  public static function getInstance()
  {
    if (is_null(self::$instance))
    {
      self::$instance = pmConfigurationPeer::retrieveByPk(1);
      if (is_null(self::$instance))
      {
        self::$instance = new self();
        self::$instance->save();
        self::$instance->loadModules();
      }
    }

    return self::$instance;
  }

  public function loadModules()
  {
    $enabled_modules = sfConfig::get('sf_enabled_modules', array());
    $always_enabled = sfConfig::get('app_pm_module_enabler_always_enabled', array());

    foreach ($enabled_modules as $enabled_module)
    {
      $pm_module = pmModulePeer::retrieveByName($enabled_module);

      if (!in_array($enabled_module, $always_enabled) && is_null($pm_module))
      {
        $pm_module = new pmModule();
        $pm_module->setName($enabled_module);
        if ($enabled_module == 'default' || $enabled_modules == 'pmconfiguration')
        {
          $pm_module->setIsEnabled(true);
        }
        $pm_module->setpmConfigurationId($this->getId());
        $pm_module->save();
      }
    }
  }

  public function isEnabled($module_name)
  {
    $ret = false;
    $always_enabled = sfConfig::get('app_pm_module_enabler_always_enabled', array());

    if ($module_name == 'pmconfiguration')
    {
      $ret = true;
    }
    else
    {
      if (!in_array($module_name, sfConfig::get('sf_enabled_modules')) || in_array($module_name, $always_enabled))
      {
        $ret = true;
      }
      else
      {
        $c = new Criteria();
        $c->add(pmModulePeer::NAME, $module_name);
        $pm_module = pmModulePeer::doSelectOne($c);
        $ret = $pm_module?$pm_module->getIsEnabled():false;
      }
    }

    return $ret;
  }

  public function getDefaults()
  {
    $defaults = array();
    foreach ($this->getpmModules() as $module)
    {
      if ($module->getIsEnabled())
      {
        $defaults[] = $module->getId();
      }
    }
    return $defaults;
  }
}
