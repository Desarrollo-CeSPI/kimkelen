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
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('default', 'module');
  }

  public function executeShowHistory(sfWebRequest $request)
  {
  		$this->student = StudentPeer::retrieveByPk($request->getParameter('student_id'));
  		$this->school_year = SchoolYearPeer::retrieveCurrent();
  		//tomo los tipos de sanciones-
  		$this->sanctions_type = SanctionTypePeer::doSelect(new Criteria());

  		$this->info = array();
  		//por cada tipo de sancion chequeo la cantidad de tiene el alumno en el año lectivo vigente.
  		foreach ($this->sanctions_type as $st) {
  			$this->info[$st->getName()] = StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctionsForStudentAndSanctionType($this->student, $st);

  		}

  		$this->link = 'student/index?student_id='.$this->student->getId();
  }

  public function executeShowReport(sfWebRequest $request){

  	$this->student = StudentPeer::retrieveByPk($request->getParameter('student_id'));
  	$this->student_disciplinary_sanctions = StudentDisciplinarySanctionPeer::retrieveStudentDisciplinarySanctionsForSchoolYear($this->student);
    $this->school_year = SchoolYearPeer::retrieveCurrent();
    $this->link = 'student_disciplinary_sanction/showHistory?student_id='.$this->student->getId();

  }
}
