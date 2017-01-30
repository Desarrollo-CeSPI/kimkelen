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
  	public function executeIndex(sfWebRequest $request)
  	{
    	$this->student = StudentPeer::retrieveByPk($request->getParameter('student_id'));
    	$this->student_career_school_year = $this->student->getCurrentStudentCareerSchoolYear();
    	
    	if(!is_null($this->student->getCurrentStudentCareerSchoolYear())){
			$this->division = DivisionPeer::retrieveByStudentCareerSchoolYear();
		}
  		$this->school_year = SchoolYearPeer::retrieveCurrent();
  		$this->link = 'student/index?student_id='.$this->student->getId();
		
	}

	public function executeShowReport(sfWebRequest $request)
	{
		$this->student = StudentPeer::retrieveByPk($request->getParameter('student_id'));
		$this->school_year = SchoolYearPeer::retrieveCurrent();
		$this->absences = StudentAttendancePeer::retrieveByStudentAndSchoolYear($this->student,$this->school_year);
		$this->link = 'student_attendance/index?student_id='.$this->student->getId();

	}		
}
