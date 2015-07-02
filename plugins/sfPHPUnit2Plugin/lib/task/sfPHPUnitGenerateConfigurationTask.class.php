<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generates a PHPUnit test file for unit tests.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage task
 *
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
class sfPHPUnitGenerateConfigurationTestTask extends sfPHPUnitGenerateBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    /*
    $this->addArguments(array(
    ));
    */

    $this->addOptions(array(
      new sfCommandOption('overwrite', null, sfCommandOption::PARAMETER_NONE, 'Forces the task to overwrite any existing configuration file'),
    ));

    $this->namespace        = 'phpunit';
    $this->name             = 'generate-configuration';
    $this->briefDescription = 'Generates the configuration xml for unit tests';
    $this->detailedDescription = <<<EOF
The [phpunit:generate-configuration|INFO] generates the default configuration xml for unit tests, which is lateron used by PHPUnit

Call it with:

  [php symfony phpunit:generate-configuration|INFO]
EOF;
  }

   /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->createBootstrap();

    $template = $this->getTemplate('phpunit.xml.dist.tpl');

    $filepath = sfConfig::get('sf_root_dir').'/phpunit.xml.dist';
    $replacePairs = array();

    $rendered = $this->renderTemplate($template, $replacePairs);

    if(!file_exists($filepath) || $options['overwrite'])
    {
      file_put_contents($filepath, $rendered);
      $this->logSection('file+', 'phpunit.xml.dist');
    }
  }
}
