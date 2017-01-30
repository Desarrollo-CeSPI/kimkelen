<?php

/**
 * califications actions.
 *
 * @package    symfony
 * @subpackage califications
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class calificationsActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeShowHistory(sfWebRequest $request)
  {
    $this->student = StudentPeer::retrieveByPk($request->getParameter('student_id'));
    $this->link = 'student/index?student_id=' . $this->student->getId();
  }
}
