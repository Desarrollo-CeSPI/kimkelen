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
  public function executeIndex(sfWebRequest $request)
  {
  	//falta chequear que sea solo los alumnos que tiene a cargo
  	$this->student = StudentPeer::retrieveByPk($request->getParameter('student_id'));
  	
  }
}
