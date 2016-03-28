<?php

/**
 * tentative_repproved_student actions.
 *
 * @package    symfony
 * @subpackage tentative_repproved_student
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class tentative_repproved_studentActions extends sfActions
{
	public function executeShow(sfWebRequest $request)
	{
		$this->school_year = $this->getRoute()->getObject();
		$this->getUser()->setReferenceFor($this);

		$this->form = new TentativeRepprovedStudentForm();
		$this->students = PathwayStudentPeer::getStudentsForSchoolYear(SchoolYearPeer::retrieveCurrent());
	}

	public function executeSave(sfWebRequest $request, $con = null)
	{
		if (!$request->isMethod("POST"))
		{
			$this->redirect('schoolyear/index');
		}

		$this->form = new TentativeRepprovedStudentForm();

		$this->form->bind($request->getParameter($this->form->getName()));

		if (is_null($con))
		{
			$con = Propel::getConnection(DivisionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($this->form->isValid())
		{
			$this->form->save($con);
			$this->getUser()->setFlash('notice', 'Los alumnos se guardaron satisfactoriamente.');
			$this->redirect('schoolyear/index');
		}
		else
		{
			$this->getUser()->setFlash('error', 'Ocurrieron errores al intentar guardar los alumnos. Por favor, intente nuevamente la operación.');
			$this->setTemplate('show');
		}

	}

	public function executeFinish(sfWebRequest $request, $con = null)
	{
    $all_tentative_repproved_students = TentativeRepprovedStudentPeer::doSelectNonDeleted();
    
    $con = is_null($con) ? Propel::getConnection() : $con;
    
	 	try
    {
      $con->beginTransaction();

      foreach ($all_tentative_repproved_students as $trs) {

      	$student_career_school_year = $trs->getStudentCareerSchoolYear();
      	$student = $student_career_school_year->getStudent();
      	
      	SchoolBehaviourFactory::getEvaluatorInstance()->repproveStudent($student, $student_career_school_year, $con);

				$trs->setIsDeleted(true);
				$trs->save();
			}

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
    }

		$this->redirect('schoolyear/index');
	}

	public function executeDeleteStudent(sfWebRequest $request, $con = null)
	{
		$ps= PathwayStudentPeer::retrieveByStudentAndSchoolYear($request->getParameter('student_id'));
    $trs = TentativeRepprovedStudentPeer::retrieveByStudentId($request->getParameter('student_id'));

		try {
			$ps->delete();
			$trs->setIsDeleted(false);
			$trs->save();
			$this->getUser()->setFlash("notice", "The item was deleted successfully.");
		} catch (PropelException $e) {
			$this->getUser()->setFlash('error', 'A problem occurs when deleting the selected items.');
		}
		$this->redirect("@school_year");
	}
}