<?php

/**
 * student_disciplinary_sanction actions.
 *
 * @package    symfony
 * @subpackage student_disciplinary_sanction
 * @author     nvidela
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */

class student_disciplinary_sanctionActions extends sfActions
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
  
	public function executeShowHistory(sfWebRequest $request)
	{
		$this->student = StudentPeer::retrieveByPk($request->getParameter('student_id'));
		$this->checkIsStudent($this->student);
		$this->school_year = SchoolYearPeer::retrieveCurrent();

		//tomo los tipos de sanciones-
		$this->sanctions_type = SanctionTypePeer::doSelect(new Criteria());

		//por cada tipo de sancion chequeo la cantidad de tiene el alumno en el año lectivo vigente.
		$this->info = array();
		$student_career_school_years = $this->student->getStudentCareerSchoolYears();
		foreach ($student_career_school_years as $scsy)
		{
		  foreach ($this->sanctions_type as $st)
		  {	
			 $sy=$scsy->getCareerSchoolYear()->getSchoolYear()->getYear();
			 $this->info[$sy][$st->getName()] = StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctionsForStudentAndSanctionTypeAndSchoolYear($this->student, $st, $sy);
		   }
		}
		
	}
}
