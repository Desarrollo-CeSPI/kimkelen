<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Launches PHPUnit functional tests.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage task
 *
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
class sfPHPUnitFunctionalTask extends sfPHPUnitBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
    new sfCommandArgument('application', sfCommandArgument::OPTIONAL, 'The application of the controller to test'),
    new sfCommandArgument('name', sfCommandArgument::OPTIONAL, 'The controller name to test')
    ));

    $this->addOptions(array(
    new sfCommandOption('options', null, sfCommandOption::PARAMETER_REQUIRED, 'Options for PHPUnit which are directly passed to the test runner'),
    new sfCommandOption('base', null, sfCommandOption::PARAMETER_REQUIRED, 'The base folder path where custom test cases are located in'),
    ));

    $this->namespace        = 'phpunit';
    $this->name             = 'test-functional';
    $this->briefDescription = 'Launches functional tests';
    $this->detailedDescription = <<<EOF
The [phpunit:test-functional|INFO] launches functional tests.
Call it with:

  [php symfony phpunit:test-functional|INFO]
EOF;
  }

  /**
   * @see sfPHPUnitBaseTask
   */
  protected function getRelativePath($arguments = array(), $options = array())
  {
    if ($options['base'])
    {
      return $options['base'].'/functional'.($arguments['application']? '/'.$arguments['application'] : '');
    }

    return 'test/phpunit/functional'.($arguments['application']? '/'.$arguments['application'] : '');
  }

  /**
   * @see sfPHPUnitBaseTask
   */
  protected function getFileSuffix()
  {
    return 'ActionsTest';
  }

  /**
   * @see sfPHPUnitBaseTask
   */
  protected function getFileSuffixWithExtension()
  {
    return 'ActionsTest.php';
  }
}
