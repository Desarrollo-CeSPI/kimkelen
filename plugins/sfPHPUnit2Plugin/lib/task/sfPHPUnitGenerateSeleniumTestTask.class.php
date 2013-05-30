<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 * (c) 2010 Richard Shank <develop@zestic.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generates a PHPUnit test file for Selenium tests.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage task
 *
 * @author     Frank Stelzer <dev@frankstelzer.de>
 * @author     Richard Shank <develop@zestic.com>
 */
class sfPHPUnitGenerateSeleniumTestTask extends sfPHPUnitGenerateBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
    new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application of the controller to test'),
    new sfCommandArgument('controller', sfCommandArgument::REQUIRED, 'The controller name to test'),
    ));

    $this->addOptions(array(
    new sfCommandOption('overwrite', null, sfCommandOption::PARAMETER_NONE, 'Forces the task to overwrite any existing files'),
    ));

    $this->namespace        = 'phpunit';
    $this->name             = 'generate-selenium';
    $this->briefDescription = 'Generates a test case for Selenium tests';
    $this->detailedDescription = <<<EOF
The [phpunit:generate-selenium|INFO] generates a test case for Selenium tests, which is lateron
executable with PHPUnit.

Call it with:

  [php symfony phpunit:generate-selenium|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->createBootstrap();

    $template = $this->getTemplate('selenium/selenium_test.tpl');

    $filename = $arguments['controller'] . 'ActionsTest.php';
    $replacePairs = array(
    '{controller_class}' => $arguments['application'] . '_' . $arguments['controller'],
    '{controller_name}' => $arguments['controller'],
    '{application}' => $arguments['application']
    );

    $rendered = $this->renderTemplate($template, $replacePairs);
    $this->saveFile($rendered, 'selenium/'.$arguments['application'].'/'.$filename, $options);

    $this->logSection('help', 'run this test with: ./symfony phpunit:test-selenium '.$arguments['application'].' '.$arguments['controller']);
  }
}
