<?php
/**
 * califications actions.
 *
 * @package    symfony
 * @subpackage califications
 * @author     Ezequiel González
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class calificationsActions extends sfActions
{
  /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  protected function checkIsStudent($student)
	{
		$tutor = TutorPeer::retrieveByUsername($this->getUser()->getUsername());
    if(is_null($student ) || ! $student->getIsTutor($tutor))
		{
			throw new sfError404Exception();
		}
	}
	
	public function executeShowHistory(sfWebRequest $request)
	{
		$this->student = StudentPeer::retrieveByPk($request->getParameter('student_id'));
		$this->checkIsStudent($this->student);
	}
}
