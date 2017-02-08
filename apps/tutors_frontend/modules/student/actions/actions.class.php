<?php

/**
 * student actions.
 *
 * @package    symfony
 * @subpackage student
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class studentActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  protected function checkIsStudent($student)
  {
    $tutor = TutorPeer::retrieveByUsername($this->getUser()->getUsername());
  	if(is_null($student ) || !$student->getIsTutor($tutor))
  	{
		throw new sfError404Exception();
	}
  }
  public function executeIndex(sfWebRequest $request)
  {
  	$this->student = StudentPeer::retrieveByPk($request->getParameter('student_id'));
  	$this->checkIsStudent($this->student);
  	
  }
}
