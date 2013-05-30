<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Launches PHPUnit unit tests.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage task
 *
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
class sfPHPUnitUnitTask extends sfPHPUnitBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
    new sfCommandArgument('name', sfCommandArgument::OPTIONAL, 'The test name'),
    ));

    $this->addOptions(array(
    new sfCommandOption('options', null, sfCommandOption::PARAMETER_REQUIRED, 'Options for PHPUnit which are directly passed to the test runner'),
    new sfCommandOption('dir', null, sfCommandOption::PARAMETER_REQUIRED, 'The subfolder the test case is located in'),
    new sfCommandOption('base', null, sfCommandOption::PARAMETER_REQUIRED, 'The base folder path where custom test cases are located in'),
    ));

    $this->namespace        = 'phpunit';
    $this->name             = 'test-unit';
    $this->briefDescription = 'Launches unit tests';
    $this->detailedDescription = <<<EOF
The [phpunit:test-unit|INFO] launches unit tests.
Call it with:

  [php symfony phpunit:test-unit|INFO]
EOF;
  }

  /**
   * @see sfPHPUnitBaseTask
   */
  protected function getRelativePath($arguments = array(), $options = array())
  {
    if ($options['base'])
    {
      return $options['base'].'/unit'.($options['dir']? '/'.$options['dir'] : '');
    }

    return 'test/phpunit/unit'.($options['dir']? '/'.$options['dir'] : '');
  }

  /**
   * @see sfPHPUnitBaseTask
   */
  protected function getFileSuffix()
  {
    return 'Test';
  }

  /**
   * @see sfPHPUnitBaseTask
   */
  protected function getFileSuffixWithExtension()
  {
    return 'Test.php';
  }
}
