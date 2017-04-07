<?php

/**
 * mainFrontend actions.
 *
 * @package    symfony
 * @subpackage mainFrontend
 * @author     ecorrons
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */

class mainFrontendActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    //tomo la info del tutor
	  $this->tutor = TutorPeer::retrieveByUsername($this->getUser()->getUsername());
	  $this->students = $this->tutor->getStudentsArray();
  }
}
