<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base class for generating PHPUnit test files.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage task
 *
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
abstract class sfPHPUnitGenerateBaseTask extends sfBaseTask
{
  /**
   * Returns plugin dir of sfPHPUnit2Plugin
   *
   * @return string
   */
  protected function getPluginDir()
  {
    return sfConfig::get('sf_plugins_dir').'/sfPHPUnit2Plugin';
  }

  /**
   * Returns data dir of project
   *
   * @return string
   */
  protected function getProjectDataDir()
  {
    return sfConfig::get('sf_data_dir');
  }

  /**
   * Returns the test dir to the PHPUnit test cases
   *
   * @return string
   */
  protected function getTestDir()
  {
    return sfConfig::get('sf_root_dir').'/test/phpunit';
  }

  /**
   * Creates the bootstrap files needed in the PHPUnit test cases
   *
   * @return bool
   */
  protected function createBootstrap()
  {

    $bootstrapDir = $this->getTestDir().'/bootstrap';
    $libDir       = $this->getTestDir().'/lib';
    $templateDir  = $this->getPluginDir().'/data/template';

    // does bootstrap dir already exists?
    if (!file_exists($bootstrapDir))
    {
      // create bootstrap dir
      $this->logSection('dir+', $bootstrapDir);
      mkdir($bootstrapDir, 0755, true);
    }

    if (!file_exists($libDir))
    {
      $this->logSection('dir+', $libDir);
      mkdir($libDir, 0755, true);
    }

    // copy bootstrap files
    $bootstrapFiles = array(
        '/unit/bootstrap.tpl' => $bootstrapDir.'/unit.php', 
        '/functional/bootstrap.tpl' => $bootstrapDir.'/functional.php', 
        '/selenium/bootstrap.tpl' => $bootstrapDir.'/selenium.php',
        '/functional/base_functional_test.tpl' => $libDir.'/BaseFunctionalTestCase.class.php',
        '/unit/base_unit_test.tpl' => $libDir.'/BaseUnitTestCase.class.php',
    );
    foreach ($bootstrapFiles as $source => $target)
    {
      if (!file_exists($target))
      {
        $this->logSection('file+', $target);
        copy($templateDir.$source, $target);
      }
    }

    return true;
  }

  /**
   * Fetches template and returns its raw content
   *
   * @param string $templateName
   *
   * @throws sfCommandException when template is not found
   *
   * @return string
   */
  protected function getTemplate($templateName)
  {
    // check if template does exist in custom dir
    $templatePath = $this->getProjectDataDir().'/sfPHPUnit2Plugin/template/' . $templateName;

    // custom template does not exist, take the plugin template
    if (!file_exists($templatePath))
    {
      $templatePath = $this->getPluginDir().'/data/template/' . $templateName;
    }

    if (!file_exists($templatePath))
    {
      throw new sfCommandException(sprintf('Template "%s" does not exist.', $templateName));
    }

    return file_get_contents($templatePath);
  }

  /**
   * Renders a template and parses assigned vars into according placeholders
   *
   * @param string $content the template content
   * @param array $replacePairs
   *
   * @return string
   */
  protected function renderTemplate($content, array $replacePairs)
  {
    return strtr($content, $replacePairs);
  }

  /**
   * Saves a rendered template to a given target file
   *
   * @param string $content the rendered template content
   * @param string $targetFile the target file
   *
   * @return int number of bytes that were written to the file, or FALSE on failure.
   */
  protected function saveFile($content, $targetFile, array $options)
  {
    $completeTarget = $this->getTestDir().'/'.$targetFile;
    $dir = dirname($completeTarget);
    if (!file_exists($dir))
    {
      $this->logSection('dir+', $dir);
      mkdir($dir, 0755, true);
    }
    $this->logSection('file+', $completeTarget);

    if (file_exists($completeTarget) && !$options['overwrite'])
    {
      throw new sfCommandException(sprintf('Test case "%s" does already exist. Use the overwrite option to force overwritting.', $targetFile));
    }

    return file_put_contents($completeTarget, $content);
  }
}
