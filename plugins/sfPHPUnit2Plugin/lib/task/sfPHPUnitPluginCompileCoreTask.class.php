<?php

/*
* This file is part of the sfPHPUnit2Plugin package.
* (c) 2010 Frank Stelzer <dev@frankstelzer.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/**
 * Compiles core classes of this plugin for external plugin usage.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage task
 *
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
class sfPHPUnitPluginCompileCoreTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
    new sfCommandArgument('target', sfCommandArgument::OPTIONAL, 'Where to save the compiled file'),
    ));

    $this->addOptions(array(
      new sfCommandOption('with-selenium', null, sfCommandOption::PARAMETER_NONE, 'Flag if selenium test case should be compiled too'),
    ));

    $this->namespace        = 'phpunit-plugin';
    $this->name             = 'compile-core';
    $this->briefDescription = 'Compiles core classes of this plugin for external plugin testing';
    $this->detailedDescription = <<<EOF
The [phpunit-plugin:compile-core|INFO] compiles core classes of this plugin for external plugin testing.
Call it with:

  [php symfony phpunit-plugin:compile-core|INFO]
EOF;
  }

  public function execute($arguments = array(), $options = array())
  {
    //plugin root + lib dir
    $libDir = realpath(dirname(__FILE__).'/../..').'/lib/test';

    $files = scandir($libDir);

    $content = '';
    foreach($files as $file)
    {
      // filter only plugin lib files
      if('sfPHPUnit' == substr($file, 0, 9))
      {
        if(!$options['with-selenium'])
        {
          // skip all Selenium classes
          if(strpos($file, 'Selenium') !== false)
          {
            continue;
          }
        }
        $content .= file_get_contents($libDir.'/'.$file);
      }
    }

    //prepend lime class because sfPHPUnitTest class extends lime_test
    $limeContent = file_get_contents(sfConfig::get('sf_symfony_lib_dir').'/vendor/lime/lime.php');
    $content = $limeContent . $content;

    // remove all starting php tags ...
    $content = str_replace('<?php', '', $content);

    // ... we only need one starting tag
    $content = '<?php ' . $content;

    if(!$arguments['target'])
    {
      $target = sfConfig::get('sf_data_dir').'/sf_phpunit_compiled.php';
    }
    else
    {
      $target = $arguments['target'];
    }
    file_put_contents($target, $content);

    // "minimize" the content ...
    $stripped = php_strip_whitespace($target);

    // ... and save it
    file_put_contents($target, $stripped);

    $this->logSection('file+', $target);
  }
}
