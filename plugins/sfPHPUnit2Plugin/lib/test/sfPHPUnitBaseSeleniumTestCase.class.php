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
 * sfBasePHPUnitBaseSeleniumTestCase is the super class for all Selenium
 * tests using PHPUnit.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage test
 * @author     Frank Stelzer <dev@frankstelzer.de>
 * @author     Richard Shank <develop@zestic.com>
 */
abstract class sfPHPUnitBaseSeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase
{
  /**
   * The sfPHPUnitTest instance for lime compatibility
   *
   * @var sfPHPUnitTest
   */
  private $test = null;

  /**
   * The sfContext instance
   *
   * @var sfContext
   */
  private $context = null;

  /**
   * Dev hook for custom "setUp" stuff
   * Overwrite it in your test class, if you have to execute stuff before a test is called.
   */
  protected function _start()
  {
  }

  /**
   * Dev hook for custom "tearDown" stuff
   * Overwrite it in your test class, if you have to execute stuff after a test is called.
   */
  protected function _end()
  {
  }

  /**
   * Please do not touch this method and use _start directly!
   */
  protected function setUp()
  {
    $this->_start();
  }

  /**
   * Please do not touch this method and use _end directly!
   */
  protected function tearDown()
  {
    $this->_end();
  }

  /**
   * A unit test does not have loaded the whole symfony context on start-up, but
   * you can create a working instance if you need it with this method
   * (taken from the bootstrap file).
   *
   * @return sfContext
   */
  protected function getContext()
  {
    if (!$this->context)
    {
      // ProjectConfiguration is already required in the bootstrap file
      $configuration = ProjectConfiguration::getApplicationConfiguration($this->getApplication(), $this->getEnvironment(), true);
      $this->context = sfContext::createInstance($configuration);
    }

    return $this->context;
  }

  /**
   * Returns current sfPHPUnitTest
   *
   * @return sfPHPUnitTest
   */
  protected function getTest()
  {
    if (!$this->test)
    {
      $this->test = new sfPHPUnitTest( $this );
    }

    return $this->test;
  }

  /**
   * Returns application name
   *
   * Overwrite this method if you need a context instance in your unit test!
   *
   * @return string
   */
  protected function getApplication()
  {
    throw new Exception( 'Application name is not defined. Overwrite "getApplication" in your unit test!');
  }

  /**
   * Returns environment name
   *
   * Overwrite this method if you need a context instance in your unit test!
   *
   * @return string test by default
   */
  protected function getEnvironment()
  {
    return 'test';
  }
}