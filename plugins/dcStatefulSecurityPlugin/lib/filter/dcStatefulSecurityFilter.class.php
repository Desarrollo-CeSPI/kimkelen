<?php

/**
 * dcStatefulSecurityFilter checks security by calling the 'can##ACTION_NAME####MODULE_NAME##()'
 * method of the user.
 *
 * @package dcStatefulSecurityPlugin
 * @author  Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class dcStatefulSecurityFilter extends sfFilter
{
  /**
   * Forwards the current request to the secure action.
   *
   * @throws sfStopException
   */
  protected function forwardToSecureAction()
  {
    $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

    throw new sfStopException();
  }

  /**
   * Retrieves an object from the given route.
   *
   * @param sfRoute $route A sfRoute instance
   */
  protected function getObjectForRoute(sfRoute $route)
  {
    if ($route instanceOf sfObjectRoute)
    {
      try
      {
        return $route->getObject();
      }
      catch(Exception $e)
      {
      }
    }
    return null;
  }

  /**
   * Executes this filter.
   *
   * @param sfFilterChain $filterChain A sfFilterChain instance
   */
  public function execute($filterChain)
  {
    // disable stateful security checking on signin and secure actions
    if (
      (sfConfig::get('sf_login_module') == $this->context->getModuleName()) && (sfConfig::get('sf_login_action') == $this->context->getActionName())
      ||
      (sfConfig::get('sf_secure_module') == $this->context->getModuleName()) && (sfConfig::get('sf_secure_action') == $this->context->getActionName())
    )
    {
      $filterChain->execute();

      return;
    }

    $sf_user = $this->context->getUser();

    // retrieve the current action
    $action = $this->context->getController()->getActionStack()->getLastEntry()->getActionInstance();

    // get the current module and action names
    $module_name = sfInflector::camelize($action->getModuleName());
    $action_name = sfInflector::camelize($action->getActionName());

    // get the object for the current route
    $object = $this->getObjectForRoute($action->getRoute());

    // i.e.: canIndexDefault
    $method = "can$action_name$module_name";

    // if the method exist
    if (method_exists($sf_user, $method))
    {
      // execute it
      if (!$sf_user->$method($object))
      {
        $this->forwardToSecureAction();
      }
    }
    else
    {
      // get the default policy
      $default_policy = $this->getParameter('default_policy', 'allow');

      // if the default policy is not 'allow'
      if ($default_policy != 'allow')
      {
        $this->forwardToSecureAction();
      }
    }

    $filterChain->execute();
    return;
  }
}
