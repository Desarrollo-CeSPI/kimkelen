<?php
/**
 * Created by PhpStorm.
 * User: Maria Emilia Corrons
 * Date: 23/02/17
 * Time: 15:36
 */
class loginFilter extends sfFilter
{
	public function execute ($filterChain)
	{
		if ($this->getContext()->getUser()->isAuthenticated())
		{
			$username = $this->getContext()->getUser()->getGuardUser()->getUsername();
			$tutor = TutorPeer::retrieveByUsername($username);
			if (!is_null($tutor)) {
				//no se puede loguear en el backend con un usuario tutor
				$this->getContext()->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
				throw new sfStopException();
			}
		}
		$filterChain->execute();
	}
}