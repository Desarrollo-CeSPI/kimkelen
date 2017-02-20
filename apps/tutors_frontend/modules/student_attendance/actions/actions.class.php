<?php

/**
 * student_attendance actions.
 *
 * @package    symfony
 * @subpackage student_attendance
 * @author     nvidela
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */

class student_attendanceActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  
	protected function checkIsStudent($student)
	{
		$tutor = TutorPeer::retrieveByUsername($this->getUser()->getUsername());
		if(is_null($student) || !$student->getIsTutor($tutor))
		{
			throw new sfError404Exception();
		}
	}
  
	public function executeIndex(sfWebRequest $request)
  {
		$this->student = StudentPeer::retrieveByPk($request->getParameter('student_id'));
		$this->checkIsStudent($this->student);
		$this->student_career_school_year = $this->student->getCurrentStudentCareerSchoolYear();

		if(!is_null($this->student->getCurrentStudentCareerSchoolYear())){
			$this->division = DivisionPeer::retrieveByStudentCareerSchoolYear($this->student_career_school_year);
		}
		$this->school_year = SchoolYearPeer::retrieveCurrent();
	}

	public function executeShowReport(sfWebRequest $request)
	{
		$this->student = StudentPeer::retrieveByPk($request->getParameter('student_id'));
		$this->checkIsStudent($this->student);
		$this->student_career_school_years = $this->student->getCurrentStudentCareerSchoolYears();
		$this->school_year = SchoolYearPeer::retrieveCurrent();
    $this->go_back = 'student_attendance/index?student_id='.$this->student->getId();
	}		
}
