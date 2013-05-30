<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The sfPHPUnitTest class simulates the functionality of the lime_test class
 * and should only be used in PHPUnit testing context.
 *
 * All public functions of the lime_test class are overwritten here and
 * are mapping to according methods of the included
 * PHPUnit_Framework_TestCase instance.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage test
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
class sfPHPUnitTest extends lime_test
{
  /**
   * PHPUnit_Framework_TestCase
   *
   * @var PHPUnit_Framework_TestCase
   */
  private $testCase = null;

  public function __construct(PHPUnit_Framework_TestCase $testCase)
  {
    $this->testCase = $testCase;
    parent::__construct(null, null);
  }

  /**
   * We are not using the output mechanism supported by lime. Therefore
   * we have to overwrite the destructor and avoid lime to output anything
   * in this way.
   *
   * @see lime_test#__destruct()
   */
  public function __destruct()
  {
    // Nothing to do here, everything else is done by PHPUnit itself.
    // If you are missing the output colors, just call
    // the phpunit command with the "--colors" option
  }

  /**
   * Only used for debugging purposes. This method should never be called,
   * otherwise you have found a bug :)
   *
   * @param string $name
   * @param array $arguments
   */
  public function __call($name, $arguments)
  {
    echo "sfPHPUnitTest::__call:{$name}:" . var_export($arguments, true) . ";\n";
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#ok()
   *
   * @param mixed $exp
   * @param string $message
   *
   * @return bool
   */
  public function ok($exp, $message = '')
  {
    $this->testCase->assertTrue((bool) $exp, $message);

    // a test will fail and abort, when exp is not true
    // however the lime interface expects a return value
    // for this method
    return true;
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#is()
   *
   */
  public function is($exp1, $exp2, $message = '')
  {
    // lime:
    //   $exp1 - actual, $exp2 - expected
    // phpunit:
    //   assertEquals($expected, $actual)
    // argument order is mixed up for phpunit

    $this->testCase->assertEquals($exp2, $exp1, $message);

    return true;
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#isnt()
   */
  public function isnt($exp1, $exp2, $message = '')
  {
    // lime:
    //   $exp1 - actual, $exp2 - expected
    // phpunit:
    //   assertNotEquals($expected, $actual)
    $this->testCase->assertNotEquals($exp2, $exp1, $message);

    return true;
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#like()
   */
  public function like($exp, $regex, $message = '')
  {
    return $this->ok(preg_match($regex, $exp), $message);
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#unlike()
   */
  public function unlike($exp, $regex, $message = '')
  {
    return $this->ok(!preg_match($regex, $exp), $message);
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#cmp_ok()
   */
  public function cmp_ok($exp1, $op, $exp2, $message = '')
  {
    eval(sprintf("\$result = \$exp1 $op \$exp2;"));
    return $this->ok($result, $message);
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#can_ok()
   */
  public function can_ok($object, $methods, $message = '')
  {
    $result = true;
    $failedMessages = array();
    foreach ((array) $methods as $method)
    {
      if (!method_exists($object, $method))
      {
        $result = false;
        $failedMessages[] = sprintf("      method '%s' does not exist", $method);
      }
    }

    if (!$result)
    {
      $this->error(implode(', ', $failedMessages));
    }
    return $this->ok($result, $message);
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#isa_ok()
   */
  public function isa_ok($var, $class, $message = '')
  {
    // PHPUnit: void assertType(string $expected, mixed $actual, string $message)
    $some = $this->testCase->assertType($class, $var, $message);
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#is_deeply()
   */
  public function is_deeply($exp1, $exp2, $message = '')
  {
    $this->error('"is_deeply" is currently not supported');
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#pass()
   */
  public function pass($message = '')
  {
    $this->testCase->assertTrue(true, $message);

    return true;
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#fail()
   */
  public function fail($message = '')
  {
    $this->testCase->fail( $message );

    return false;
  }

  /**
   * http://www.phpunit.de/manual/current/en/incomplete-and-skipped-tests.html
   *
   * @see lime_test#skip()
   *
   * @param string $message
   * @param int $nb_tests
   */
  public function skip($message = '', $nb_tests = 1)
  {
    $this->testCase->markTestSkipped($message . ($message ? ', ' : '') . 'nb_tests: ' . $nb_tests);
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#include_ok()
   */
  public function include_ok($file, $message = '')
  {
    return $this->ok((@include($file)) == 1, $message);
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#comment()
   */
  public function comment($msg)
  {
    $this->diag('COMMENT: ' . $msg);
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#info()
   */
  public function info($msg)
  {
    $this->diag('INFO: ' . $msg);
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#error()
   */
  public function error($msg, $file = null, $line = null, array $traces = array())
  {
    $this->diag('ERROR: ' . $msg);
  }

  /**
   * Overwritten lime_test method
   *
   * @see lime_test#diag()
   */
  public function diag($msg)
  {
    // only output something, when a non-empty message is given
    if ($msg)
    {
      // PHPUnit does not support a native output method,
      // so do a normal echo call here.
      echo "\n# $msg";
    }
  }
}
