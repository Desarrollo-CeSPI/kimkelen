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
class sfPHPUnitGenerateUnitTestTask extends sfPHPUnitGenerateBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('name', sfCommandArgument::REQUIRED, 'The unit test file name'),
    ));

    $this->addOptions(array(
      new sfCommandOption('overwrite', null, sfCommandOption::PARAMETER_NONE, 'Forces the task to overwrite any existing files'),
      new sfCommandOption('dir', null, sfCommandOption::PARAMETER_REQUIRED, 'A subfolder name, where the unit test should be saved to'),
    ));

    $this->namespace        = 'phpunit';
    $this->name             = 'generate-unit';
    $this->briefDescription = 'Generates a test case for unit tests';
    $this->detailedDescription = <<<EOF
The [phpunit:generate-unit|INFO] generates a test case for unit tests, which is lateron
executable with PHPUnit.

Call it with:

  [php symfony phpunit:generate-unit|INFO]
EOF;
  }

   /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->createBootstrap();

    $template = $this->getTemplate('unit/unit_test.tpl');

    $filename = $arguments['name'] . 'Test.php';
    $replacePairs = array(
    '{test_class}' => $arguments['name'],
    '{dir}' => $options['dir']? '/../..' : '/..',
    );

    $rendered = $this->renderTemplate($template, $replacePairs);
    $this->saveFile($rendered, 'unit/'.($options['dir']? $options['dir'].'/' : '').$filename, $options);

    $optionsStr = '';
    if ($options['dir'])
    {
      $optionsStr .='--dir="'.$options['dir'].'" ';
    }

    $this->logSection('help', 'run this test with: ./symfony phpunit:test-unit '.$optionsStr.$arguments['name']);
  }
}
