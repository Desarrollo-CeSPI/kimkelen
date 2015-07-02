<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base class for unit or functional tests.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage task
 *
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
abstract class sfPHPUnitBaseTask extends sfBaseTask
{
  /**
   * Returns relative path to the test to run
   *
   * @param array $arguments the task arguments
   * @param array $options the task options
   *
   * @return string
   */
  abstract protected function getRelativePath($arguments = array(), $options = array());

  /**
   * Returns file suffix
   *
   * @return string
   */
  abstract protected function getFileSuffix();

  /**
   * Returns file suffix with file extension
   *
   * @return string
   */
  abstract protected function getFileSuffixWithExtension();

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {

    if(!strpos($options['options'], '--colors'))
    {
      $options['options'] .= ' --colors';
    }

    $relativePath = $this->getRelativePath($arguments, $options);

    $tmpName = $arguments['name'];

    $name = '';

    // do some magic and check if given test name is ok
    // or if some suffix has to be added due to the naming convention of test cases
    // should we run a single file, a directory or even everything?
    if ($tmpName)
    {
      // should we run a subdirectory?
      if ('/' == substr($tmpName,-1))
      {
        // do nothing here
      }
      // is only the "Test" suffix existing?
      // check for "SomeTest" --> "SomeTest.php"
      elseif (preg_match('/^.*'.$this->getFileSuffix().'$/s', $tmpName, $hits))
      {
        $name = $tmpName.'.php';
      }
      // is the "Test" suffix with extension already included in the name?
      // check for "SomeTest.php"
      elseif (false === strpos($tmpName, $this->getFileSuffixWithExtension()))
      {
        $name = $tmpName.$this->getFileSuffixWithExtension();
      }

      // file name is ok now for PHPUnit
      $path = $relativePath.'/'.$name;
    }
    else
    {
      $path = $relativePath;
    }

    $cmd = 'phpunit '.$options['options'].' '.escapeshellarg($path);

    // $this->logSection('debug', $cmd);

    $output = '';

    passthru($cmd, $output);

    return $output;
  }
}
