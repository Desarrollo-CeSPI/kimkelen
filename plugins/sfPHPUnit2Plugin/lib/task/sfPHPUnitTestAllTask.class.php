<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Launches all PHPUnit tests.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage task
 *
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
class sfPHPUnitTestAllTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    //$this->addArguments(array(
    //));

    $this->addOptions(array(
    new sfCommandOption('options', null, sfCommandOption::PARAMETER_REQUIRED, 'Options for PHPUnit which are directly passed to the test runner'),
    new sfCommandOption('configuration', null, sfCommandOption::PARAMETER_NONE, 'Flag if default configuration of phpunit.xml should be used'),
    ));

    $this->namespace        = 'phpunit';
    $this->name             = 'test-all';
    $this->briefDescription = 'Launches all PHPUnit tests';
    $this->detailedDescription = <<<EOF
The [phpunit:test-all|INFO] launches all PHPUnit tests.
Call it with:

  [php symfony phpunit:test-all|INFO]

Note: This task should be run with the process-isolation option which is available in PHPUnit 3.4.

  [php symfony phpunit:test-all --options="--process-isolation"|INFO]
EOF;
  }

   /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $cmd = 'phpunit '.$options['options'].($options['configuration']? '' : ' test/phpunit/');

    $output = '';

    passthru($cmd, $output);

    return $output;
  }
}
