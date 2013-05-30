<?php

/**
 * Description of sfGuardUserCustomFormFilter
 *
 * @author gramirez
 */
class sfGuardUserCustomFormFilter  extends sfGuardUserFormFilter
{
  public function configure()
  {
    parent::configure();
    unset($this['created_at'], $this['last_login'], $this['is_active'], $this['is_super_admin'], $this['change_password_at'], $this['must_change_password'], $this['sf_guard_user_permission_list']);    
    
    $this->getWidget('username')->setOption('with_empty', false);
  }
}
