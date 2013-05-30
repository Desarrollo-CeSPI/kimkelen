<?php

/**
 * pmConfiguration form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class pmConfigurationForm extends BasepmConfigurationForm
{
  public function configure()
  {
    unset(
      $this["created_at"],
      $this["updated_at"]
    );

    $this->widgetSchema["enabled_modules"] = new sfWidgetFormPropelChoice(array(
      "model" => "pmModule",
      "multiple" => true,
      "expanded" => true
    ));
    
    $this->validatorSchema["enabled_modules"] = new sfValidatorPropelChoice(array(
      "model" => "pmModule",
      "multiple" => true,
      "required" => false
    ));
    
    $this->widgetSchema['enabled_modules']->setDefault($this->getObject()->getDefaults());
  }

  public function doSave($con = null)
  {
    parent::doSave($con);

    $enabled_ids = $this->getValue('enabled_modules');

    $all_dependencies = sfConfig::get('app_pm_module_enabler_dependencies', array());

    foreach ($this->getObject()->getpmModules() as $pm_module)
    {
      $pm_module->setIsEnabled((in_array($pm_module->getId(), $enabled_ids) || $pm_module->getName() == 'pmconfiguration')?true:false);
      $pm_module->save();
    }

    foreach ($all_dependencies as $pm_module_name => $dependency)
    {
      $pm_module = pmModulePeer::retrieveByName($pm_module_name);
      if ($pm_module->getIsEnabled())
      {
        $dependencies = isset($all_dependencies[$pm_module_name])?$all_dependencies[$pm_module_name]:array();
        // save all module dependencies
        foreach ($dependencies as $dependency)
        {
          $dependency = pmModulePeer::retrieveByName($dependency);
          $dependency->setIsEnabled(true);
          $dependency->save();
        }
      }
    }

    // clear cache
    $cache = new sfFileCache(array('cache_dir' => sfConfig::get('sf_cache_dir')));
    $cache->clean();
  }
}
