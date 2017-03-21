<?php

class sfGuardAuthComponents extends sfComponents
{

	public function executeDoLogin(sfWebRequest $request)
	{
		// $this->getOpenIdForGoogle();
		$this->form = new sfGuardFormSignin();
	}

}