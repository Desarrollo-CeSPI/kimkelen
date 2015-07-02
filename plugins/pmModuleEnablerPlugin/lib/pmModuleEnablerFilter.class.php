<?php

class pmModuleEnablerFilter extends sfFilter
{
  public function execute($filterChain)
  {
    if (
      (sfConfig::get('sf_login_module') == $this->context->getModuleName())
      ||
      (sfConfig::get('sf_secure_module') == $this->context->getModuleName()) && (sfConfig::get('sf_secure_action') == $this->context->getActionName())
    )
    {
      return $filterChain->execute();
    }

    $module_name = $this->context->getModuleName();
    if (!pmConfiguration::getInstance()->isEnabled($module_name))
    {
      $this->context->getController()->getActionStack()->getLastEntry()->getActionInstance()->forward404();
    }

    return $filterChain->execute();
  }
}
