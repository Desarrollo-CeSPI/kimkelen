<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Custom controller for functional tests with this plugin.
 * The exception handling of the original sfFrontWebController is not compatible
 * with PHPUnit, if process-isolation is activated.
 * 
 * Most part of this code is copy&pasted from sfFrontWebController.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage test
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
class sfPHPUnit2FrontWebController extends sfWebController{
  /**
   * Dispatches a request.
   *
   * This will determine which module and action to use by request parameters specified by the user.
   */
  public function dispatch()
  {
    try
    {
      // reinitialize filters (needed for unit and functional tests)
      sfFilter::$filterCalled = array();

      // determine our module and action
      $request    = $this->context->getRequest();
      $moduleName = $request->getParameter('module');
      $actionName = $request->getParameter('action');

      if (empty($moduleName) || empty($actionName))
      {
        throw new sfError404Exception(sprintf('Empty module and/or action after parsing the URL "%s" (%s/%s).', $request->getPathInfo(), $moduleName, $actionName));
      }

      // make the first request
      $this->forward($moduleName, $actionName);
    }
    catch (sfStopException $e)
    {
        // ignore, do nothing
    }
    catch (Exception $e)
    {
      // Throwing an exception here will break the exception handling
      // when process isolation of PHPUnit is activated.
      // This will only work when the configuration "convertErrorsToExceptions"
      // is enabled.
      trigger_error(sprintf('wrapped controller exception [%s]: %s', get_class($e), $e->getMessage()));
    }
  }
}

