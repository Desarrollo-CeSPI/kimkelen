<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGuardBasicSecurityFilter.class.php 9999 2008-06-29 21:24:44Z fabien $
 */
class sfGuardChangePasswordFilter extends sfFilter
{
  public function execute ($filterChain)
  {
    if (
        $this->getContext()->getUser()->isAuthenticated() &&
        $this->getContext()->getUser()->getGuardUser()->getMustChangePassword()&&
        !(($this->context->getModuleName() == 'sfGuardChangePassword') && ($this->context->getActionName() == 'index') )
      )
    {
          $this->getContext()->getController()->forward('sfGuardChangePassword','index');
          throw new sfStopException();
    }
   
    $filterChain->execute();
  }
}
