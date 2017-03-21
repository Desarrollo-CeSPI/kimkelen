<?php

/**
 * Created by PhpStorm.
 * User: María Emilia Corrons
 * Date: 23/02/17
 * Time: 15:37
 */
class frontendLoginFilter extends sfFilter
{
	public function execute ($filterChain)
	{
		if ($this->getContext()->getUser()->isAuthenticated())
		{
			$user = $this->getContext()->getUser();
			$username = $user->getGuardUser()->getUsername();
			$tutor = TutorPeer::retrieveByUsername($username);
			if (is_null($tutor)) {
				//no se puede loguear en el frontend
				//$module = sfConfig::get('sf_login_module');
				//return $this->getContext()->getController()->redirect($module.'/'.sfConfig::get('sf_login_action'));
				throw new sfStopException();
			}

		}
		$filterChain->execute();
	}
}