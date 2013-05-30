<?php

/**
 * sfPhpExcelPluginConfiguration configuration.
 * 
 * @package     sfSwiftMailer4Plugin
 * @subpackage  config
 * @author      Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 * @version     SVN: $Id$
 */
 
class sfPhpExcelPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    sfToolkit::addIncludePath(array(
      realpath(dirname(__FILE__).'/../lib/PHPExcel'),
    ));

    if ($this->configuration instanceof sfApplicationConfiguration)
    {
      if ($file = $this->configuration->getConfigCache()->checkConfig('config/phpexcel.yml', true))
      {
        include($file);
      }
    }
  }
}