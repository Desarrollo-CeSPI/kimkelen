<?php
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php

class BaseEvaluatorBehaviour extends InterfaceEvaluatorBehaviour
{

  const PROMOTION_NOTE = 7;
  const MIN_NOTE = 4;
  const POSTPONED_NOTE = 4;
  const DECEMBER = 1;
  const FEBRUARY = 2;
  const MAX_DISAPPROVED = 2;
  const EXAMINATION_NOTE = 6;
  const MINIMUN_MARK = 0; //nota minima de un examen
  const MAXIMUN_MARK = 10; //nota maxima de un examen
  const EXEMPT = 'Eximido';
  
  const APPROVED = 1;
  const DISAPPROVED = 2;
  const ABSENT = 3;

	const PATHWAY_PROMOTION_NOTE = 7;

  protected
  $_examination_number = array(
    self::DECEMBER => 'Diciembre',
    self::FEBRUARY => 'Febrero',
  );
  protected
  $_examination_number_short = array(
    self::DECEMBER => 'Reg',
    self::FEBRUARY => 'Comp',
  );
  
  protected
  $_result_string = array(
    self::APPROVED => 'Aprobado',
    self::DISAPPROVED => 'Desaprobado',
    self::ABSENT => 'Ausente'
  );

  public function getExaminationNumbers()
  {
    return $this->_examination_number_short;
  }


	public function getExaminationNumbersLong()
	{
		return $this->_examination_number;
	}

  /*
   * Returns if a student has approved or not the course subject
   *
   * @param CourseSubjectStudent $course_subject_student
   * @param PropelPDO $con
   *
   * @return Object $object
   * Este metodo se fija que la nota del promedio sea mayor o igual que el minimo de aprobacion de la carrera
   * y que la ultima nota no sea un aplazo (menor que self::POSTPONED_NOTE)
   */

  public function isApproved(CourseSubjectStudent $course_subject_student, $average, PropelPDO $con = null)
  {
    $minimum_mark = $course_subject_student->getCourseSubject($con)->getCareerSubjectSchoolYear($con)->getConfiguration($con)->getCourseMinimunMark();
    return $average >= $minimum_mark
      && $course_subject_student->getMarkFor($course_subject_student->countCourseSubjectStudentMarks(null, false, $con), $con)->getMark() > $this->getPosponedNote();

  }

  public function getCourseSubjectStudentResult(CourseSubjectStudent $course_subject_student, PropelPDO $con = null)
  {
    $average = $course_subject_student->getMarksAverage($con);
    
    if ($this->isApproved($course_subject_student, $average, $con))
    {
      return $this->createStudentApprovedCourseSubject($course_subject_student, $average, $con);
    }
    else
    {
      $student_disapproved_course_subject = new StudentDisapprovedCourseSubject();
      $student_disapproved_course_subject->setCourseSubjectStudent($course_subject_student);
      $student_disapproved_course_subject->setExaminationNumber($this->getExaminationNumberFor($average, false, $course_subject_student));

      return $student_disapproved_course_subject;
    }

  }

  public function getExaminationNumberFor($average, $is_free = false, $course_subject_student = null)
  {
    return (($average >= self::MIN_NOTE)) ? self::DECEMBER : self::FEBRUARY;

  }

  public function getStringFor($key)
  {
    return $this->_examination_number[$key];

  }

  public function getShortStringFor($key)
  {
    return $this->_examination_number_short[$key];

  }

  public function evaluateCareerSchoolYearStudent(CareerSchoolYear $career_school_year, Student $student, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;
    // obtenemos las materias de este año
    #$course_subject_students = CourseSubjectStudentPeer::retrieveByCareerSchoolYearAndStudent($career_school_year, $student, $con);
    $c = CourseSubjectStudentPeer::retrieveCriteriaByCareerSchoolYearAndStudent($career_school_year, $student, $con);
    $pager = new sfPropelPager('CourseSubjectStudent', 100);
    $pager->setCriteria($c);
    $pager->init();

    for ($i = 1; $i < $pager->getLastPage() + 1; $i++)
    {
      $course_subject_students = $pager->getResults();
      // para todas las materias cursadas este año
      foreach ($course_subject_students as $course_subject_student)
      {
        // obtenemos el resultado (aprobada o desaprobada) y la cerramos.
        // para el caso de las aprobadas, se crea la mesa de examen final (StudentApprovedCareerSubject)
        // de lo contrario, la inscripción a la mesa de examen (TODO)
        $result = $course_subject_student->getCourseResult($con);

        if (!is_null($result))
        {
          $result->close($con);
          $log = new LogCloseCareerSchoolYear();
          $log->setCourseSubjectStudent($course_subject_student);
          $log->setCourseResult(get_class($result));
          $log->setCourseResultId($result->getId());
          $log->setUsername(sfContext::getInstance()->getUser());
          
          $log->save();
          $log->clearAllReferences(true);
          
        }
      }
      ###Liberando memoria ###
      CourseSubjectStudentPeer::clearInstancePool();
      StudentApprovedCourseSubjectPeer::clearInstancePool();
      StudentDisapprovedCourseSubjectPeer::clearInstancePool();
      ################################
      $pager->setPage($pager->getPage() + 1);
      $pager->init();
    }
    unset($pager);
    unset($c);

  }

  /**
   * This method checks conditions of repetition.
   *
   * @param Student $student
   * @param StudentCareerSchoolYear $student_career_school_year
   * @return boolean
   */
  public function checkRepeationCondition(Student $student, StudentCareerSchoolYear $student_career_school_year)
  {
    //If current year is the last year of the career.
    if ($student_career_school_year->isLastYear())
    {
      return false;
    }

    $career_school_year = $student_career_school_year->getCareerSchoolYear();
    $last_year_previous = StudentRepprovedCourseSubjectPeer::countRepprovedForStudentAndCareerAndYear($student, $career_school_year->getCareer(), $student_career_school_year->getYear() - 1);

    if ($last_year_previous > 0)
    {
      return true;
    }

    //If Previous count > max count of repproved subject allowed, then the student will repeat or go to pathways programs
    $previous = StudentRepprovedCourseSubjectPeer::countRepprovedForStudentAndCareer($student, $student_career_school_year->getCareerSchoolYear()->getCareer());

    return ($previous > $career_school_year->getSubjectConfiguration()->getMaxPrevious());

  }

  public function saveTentativeRepprovedStudent($student_career_school_year, PropelPDO $con = null)
  {
    $tentative_repproved_student = new TentativeRepprovedStudent();
	  $tentative_repproved_student->setStudentCareerSchoolYear($student_career_school_year);
	  $tentative_repproved_student->save($con);
  }

	public function repproveStudent(Student $student, $student_career_school_year, PropelPDO $con = null){
    $student_career_school_year->setStatus(StudentCareerSchoolYearStatus::REPPROVED);
    $student_career_school_year->save($con);
    $career_school_year = $student_career_school_year->getCareerSchoolYear();

    // se eliminan las previas que se habian generado en este año de cursada
    $c = new Criteria();
    $c->addJoin(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());

    foreach (StudentRepprovedCourseSubjectPeer::doSelect($c, $con) as $repproved)
    {   // en el caso de haber rendido libre esas calificaciones no se borran
        $sers = StudentExaminationRepprovedSubjectPeer::retrieveByStudentRepprovedCourseSubject($repproved);
        if(is_null($sers))
        {
             $repproved->delete($con);
        }
    }

  }

  public function stepToNextYear(Student $student, SchoolYear $school_year, PropelPDO $con = null)
  {

    $student_career_school_years = StudentCareerSchoolYearPeer::retrieveCareerSchoolYearForStudentAndYear($student, $school_year);

    foreach ($student_career_school_years as $student_career_school_year)
    {
      $career_school_year = $student_career_school_year->getCareerSchoolYear();

      if ($this->checkRepeationCondition($student, $student_career_school_year))
      {
        $this->saveTentativeRepprovedStudent($student_career_school_year);
      }
      else
      {
        $career_student = CareerStudentPeer::retrieveByCareerAndStudent($career_school_year->getCareerId(), $student->getId());

        // solo se chequea si existe esto, por que cuando viene desde una mesa de previa no tiene por que existir el student_career_school_year
        $next_year = $student_career_school_year->getYear() + 1;

        $previous = StudentRepprovedCourseSubjectPeer::countRepprovedForStudentAndCareer($student, $student_career_school_year->getCareerSchoolYear()->getCareer());
       
        if ($next_year > $career_student->getCareer()->getMaxYear())
        {	//EGRESADO
			if( $previous == 0)
			{  
				  $career_student->setStatus(CareerStudentStatus::GRADUATE);
				  //se guarda el school_year en que termino esta carrera
				  $career_student->setGraduationSchoolYearId($school_year->getId());
				  $career_student->save($con);
				  
				  $student_career_school_year->setStatus(StudentCareerSchoolYearStatus::APPROVED);
				  $student_career_school_year->save($con);
			}
			else
			{//LIBRE
				$student_career_school_year->setStatus(StudentCareerSchoolYearStatus::FREE);
				$student_career_school_year->save($con);
			}
         
        }
        //Si no fue aprobado ya.
        elseif ($student_career_school_year->getStatus() != StudentCareerSchoolYearStatus::APPROVED)
        {
          // Elimina los Allowed y Allowed Pathway del alumno.
          $student->deleteAllCareerSubjectAlloweds($con);
          $student->deleteAllCareerSubjectAllowedPathways($con);
          //Se agregan las materias que puede cursar el alumno.
          $career_student->createStudentsCareerSubjectAlloweds($next_year, $con);
          
          // y decimos que aprobó
		  $student_career_school_year->setStatus(StudentCareerSchoolYearStatus::APPROVED);
		  $student_career_school_year->save($con);
        } 
      }
    }

    return true;

  }

  public function closeCourseSubjectStudent($result, PropelPDO $con = null)
  {
    if ($result instanceof StudentApprovedCourseSubject)
    {
      if (is_null($student_approved_career_subject = $result->getStudentApprovedCareerSubject($con)))
      {
        $student_approved_career_subject = new StudentApprovedCareerSubject();
        $student_approved_career_subject->setCareerSubject($result->getCourseSubject($con)->getCareerSubject($con));
        $student_approved_career_subject->setStudent($result->getStudent($con));
        $student_approved_career_subject->setSchoolYear($result->getSchoolYear($con));
        $student_approved_career_subject->setMark($result->getMark());

        $result->setStudentApprovedCareerSubject($student_approved_career_subject);

        $student_approved_career_subject->save($con);
        $result->save($con);

        $student_approved_career_subject->clearAllReferences(true);

        $result->clearAllReferences(true);
      }

      unset($result);
      unset($student_approved_career_subject);
    }
    else
    {
		
      $c = new Criteria();
      $c->add(CourseSubjectStudentExaminationPeer::EXAMINATION_NUMBER, $result->getExaminationNumber());
      $c->add(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, $result->getCourseSubjectStudent()->getId());
      if (CourseSubjectStudentExaminationPeer::doCount($c) == 0)
      {
        $this->createCourseSubjectStudentExamination($result->getCourseSubjectStudent(null, $con), $con);
      }
    }
  }

  public function createCourseSubjectStudentExamination(CourseSubjectStudent $course_subject_student, $con)
  {
    $course_subject_student_examination = new CourseSubjectStudentExamination();
    $course_subject_student_examination->setCourseSubjectStudent($course_subject_student);
//El if creo que no deberia existir para mantener la integridad de los datos. no deberia  existir course_subject_student_examination sin un examinationNumbre
//    if (!is_null($course_subject_student->getCourseResult()))
//    {

    $course_result = $this->getCourseSubjectStudentResult($course_subject_student, $con);

    $examination_number = $course_result->getExaminationNumber();
    $course_subject_student_examination->setExaminationNumber($examination_number);
//    }
    $course_subject_student_examination->save($con);
    //Libero memoria
    $course_subject_student_examination->clearAllReferences(true);
    unset($course_subject_student_examination);
    unset($examination_number);
  }

  public function closeCourseSubjectStudentExamination(CourseSubjectStudentExamination $course_subject_student_examination, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    $course_subject_student = $course_subject_student_examination->getCourseSubjectStudent();
    
    // si aprueba la mesa de examen
    if ($course_subject_student_examination->getMark() >= $this->getExaminationNote())
    {
      $result = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student, $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear());

      if (is_null($result))
      {
        $result = new StudentApprovedCareerSubject();
        $result->setCareerSubject($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubject());
        $result->setStudent($course_subject_student->getStudent());
        $result->setSchoolYear($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear());

        //Se busca si había una previa creada para esta materia entonces se debe eliminar ya que ahora está aprobada
        if ($student_repproved_course_subject = StudentRepprovedCourseSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student))
        {
          $sers = $student_repproved_course_subject->getStudentExaminationRepprovedSubjects();
	        //$sers = StudentExaminationRepprovedSubjectPeer::retrieveByStudentRepprovedCourseSubject($student_repproved_course_subject);

	        if ($sers >= 1) 
          {
	          foreach ($sers as $student_examination_repproved_subject) 
            {
              $student_examination_repproved_subject->delete($con);
            }
          }
          $student_repproved_course_subject->delete($con);
          
        }
      }

      $examination_subject = $course_subject_student_examination->getExaminationSubject();

      // IF is null, is because the course_subject_student_examination has been created editing student history
      $school_year = is_null($examination_subject) ? $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear() : $examination_subject->getExamination()->getSchoolYear();

      $result->setSchoolYearId($school_year->getId());

      $average = $this->getAverage($course_subject_student, $course_subject_student_examination);

      $average = sprintf('%.4s', $average);

      if ($average < 4)
      {
        $average = 4;
      }

      // se guarda la NOTA FINAL de la materia
      if ($course_subject_student_examination->getExaminationNumber() == self::FEBRUARY)
      {
        $this->setFebruaryApprovedResult($result, $average, $course_subject_student_examination->getMark());
      }
      else
      {
        $result->setMark($average);
      }

      ##se agrega en la tupla student_disapproved_course_subject el link a al resultado final y el tipo de mesa en el que aprobo
      $sdcs = $course_subject_student->getCourseResult();
      $sdcs->setStudentApprovedCareerSubject($result);
      $sdcs->setExaminationNumber($course_subject_student_examination->getExaminationNumber());
      $sdcs->save($con);

      $result->save($con);

    }
    else
    {
      // TODO: arreglar esto: pedir a la configuración
      // Pasa de diciembre a febrero (se copia el course_subject_student_examination con examination_number + 1)
      if ($course_subject_student_examination->getExaminationNumber() < count($this->_examination_number))
      {
        $this->nextCourseSubjectStudentExamination($course_subject_student_examination, $con);
      }
      else
      {
        // se crea una previa
        $srcs = StudentRepprovedCourseSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student);

        if (is_null($srcs)) {
           $student_repproved_course_subject = new StudentRepprovedCourseSubject();
           $student_repproved_course_subject->setCourseSubjectStudentId($course_subject_student->getId());
           $student_repproved_course_subject->save($con);

        }
      }
    }

  }

  public function getExaminationResult(CourseSubjectStudentExamination $css_examination)
  {
    if ($css_examination->getMark())
    {

      if ($css_examination->getMark() < $this->getExaminationNote())
      {
        if ($css_examination->getExaminationNumber() < count($this->_examination_number))
        {
          $current = $this->_examination_number[$css_examination->getExaminationNumber()];
          $next = $this->_examination_number[$css_examination->getExaminationNumber() + 1];
          return array(strtolower($current), $next);
        }
        else
        {
          return array(strtolower($this->_examination_number[$css_examination->getExaminationNumber()]), "Previous");
        }
      }
      else
      {
        return array("approved", "Approved");
      }
    }
    else
    {
      return array("absent", $css_examination->getExaminationNumber() < count($this->_examination_number) ? $this->_examination_number[$css_examination->getExaminationNumber() + 1] : "Previous");
    }

  }

  public function getExaminationRepprovedResult(StudentExaminationRepprovedSubject $student_examination_repproved_subject)
  {
    return ($student_examination_repproved_subject->getMark() >= $this->getExaminationNote()) ? array("approved", "Approved") : array('disapproved', 'Disapproved');

  }

  /**
   * If the student approves the previous, then it creates a student_approved_career_subject for this student
   *
   * @param StudentExaminationRepprovedSubject $student_examination_repproved_subject
   * @param PropelPDO $con
   */
	public function closeStudentExaminationRepprovedSubject(StudentExaminationRepprovedSubject $student_examination_repproved_subject, PropelPDO $con)
	{
		if ($student_examination_repproved_subject->getMark() >= $this->getExaminationNote())
		{
			$student_approved_career_subject = new StudentApprovedCareerSubject();
                        $car_sub = $student_examination_repproved_subject->getStudentRepprovedCourseSubject()->getCourseSubjectStudent()->getCourseSubject()->getCareerSubject();
                        $student_approved_career_subject->setCareerSubject($car_sub);
			$student_approved_career_subject->setStudent($student_examination_repproved_subject->getStudent());
			$student_approved_career_subject->setSchoolYear($student_examination_repproved_subject->getExaminationRepprovedSubject()->getExaminationRepproved()->getSchoolYear());

			//Final average is the average of the course_subject_student and the mark of student_examination_repproved_subject
			$average = (string) (($student_examination_repproved_subject->getStudentRepprovedCourseSubject()->getCourseSubjectStudent()->getMarksAverage() + $student_examination_repproved_subject->getMark()) / 2);

			$average = sprintf('%.4s', $average);
			if ($average < self::MIN_NOTE)
			{
				$average = self::MIN_NOTE;
			}
			$student_approved_career_subject->setMark($average);

			$student_repproved_course_subject = $student_examination_repproved_subject->getStudentRepprovedCourseSubject();
			$student_repproved_course_subject->setStudentApprovedCareerSubject($student_approved_career_subject);
			$student_repproved_course_subject->save($con);

			$career = $student_repproved_course_subject->getCourseSubjectStudent()->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getCareer();
			##se corrobora si la previa es la última y está libre, hay que egresarlo
			$previous = StudentRepprovedCourseSubjectPeer::countRepprovedForStudentAndCareer($student_repproved_course_subject->getStudent(), $career);
			if ($student_repproved_course_subject->getStudent()->getCurrentOrLastStudentCareerSchoolYear()->getStatus() == StudentCareerSchoolYearStatus::FREE && $previous == 0)
			{
				$career_student = CareerStudentPeer::retrieveByCareerAndStudent($career->getId(), $student_repproved_course_subject->getStudent()->getId());;
				$career_student->setStatus(CareerStudentStatus::GRADUATE);
				//se guarda el school_year en que termino esta carrera
				$career_student->setGraduationSchoolYearId(SchoolYearPeer::retrieveCurrent()->getId());
				$career_student->save($con);
				//se guarda el estado en el student_career_school_year
				$scsy = $student_repproved_course_subject->getCourseSubjectStudent()->getStudent()->getCurrentOrLastStudentCareerSchoolYear();
				$scsy->setStatus(StudentCareerSchoolYearStatus::APPROVED);
				$scsy->save();
			}

			##se agrega el campo en student_disapproved_course_subject a el link del resultado final
			$student_repproved_course_subject->getCourseSubjectStudent()->getCourseResult()->setStudentApprovedCareerSubject($student_approved_career_subject)->save($con);

			$student_approved_career_subject->save($con);
		}

	}






	/**
    * This method returns a string for the result.
    * @param StudentRepprovedCourseSubject $student_repproved_course_subject
    * @return String
    */
   public function getStudentRepprovedResultString(StudentRepprovedCourseSubject $student_repproved_course_subject)
   {
     return __('Previous') . "/" . __('Free');
   }

  /**
   * This method returns a string for the result.
   * @param StudentApprovedCourseSubject $student_approved_course_subject
   * @return String
   */
  public function getStudentApprovedResultString(StudentApprovedCourseSubject $student_approved_course_subject)
  {
    return __('Approved');

  }

  /**
   * This method returns a string for the result.
   * @param StudentApprovedCourseSubject $student_approved_course_subject
   * @return String
   */
  public function getStudentDisapprovedResultString(StudentDisapprovedCourseSubject $student_disapproved_course_subject)
  {
    return $this->getStringFor($student_disapproved_course_subject->getExaminationNumber());

  }

  /**
   * This method returns a string for the result.
   * @param StudentApprovedCourseSubject $student_approved_course_subject
   * @return String
   */
  public function getStudentDisapprovedResultStringShort(StudentDisapprovedCourseSubject $student_disapproved_course_subject)
  {

    $configuration = $student_disapproved_course_subject->getCareerSchoolYear()->getSubjectConfiguration();
    if ($configuration->getWhenDisapproveShowString())
    {
      return sprintf("%01.2f", $student_disapproved_course_subject->getCourseSubjectStudent()->getMarksAverage());
    }
    else
    {
      return $this->getShortStringFor($student_disapproved_course_subject->getExaminationNumber());
    }

  }

  /**
   * This method returns the available marks for students. For Bba behavior like, depends of the closed notes.
   *
   * @param CourseSubjectStudent $course_subject_student
   * @return <type>
   */
  public function getAvailableCourseSubjectStudentMarks(CourseSubjectStudent $course_subject_student, Criteria $c = null)
  {
    if (is_null($c))
    {
      $c = new Criteria();
    }

    $c->addAscendingOrderByColumn(CourseSubjectStudentMarkPeer::MARK_NUMBER);
    $availables = array();
    $course_subject_student_marks = $course_subject_student->getCourseSubjectStudentMarks($c);
    $availables[1] = array_shift($course_subject_student_marks);
    foreach ($course_subject_student_marks as $cssm)
    {
      if (isset($availables[$cssm->getMarkNumber() - 1]) && $availables[$cssm->getMarkNumber() - 1]->getIsClosed())
        $availables[$cssm->getMarkNumber()] = $cssm;
    }

    return $availables;

  }

  /**
   * This method returns the marks average of a student.
   *
   * @param CourseSubjectStudent $course_subject_student
   * @return <type>
   */
  public function getMarksAverage($course_subject_student, PropelPDO $con = null)
  {
    $sum = 0;
    foreach ($course_subject_student->getCourseSubjectStudentMarks(null, $con) as $cssm)
    {
      $sum += $cssm->getMark();
    }

    $average = (string) ($sum / $course_subject_student->countCourseSubjectStudentMarks(null, false, $con));
    $average = sprintf('%.4s', $average);
    return $average;
  }

  /**
   * This method is a hack for a special subject in LVM behavior (Historia del arte)
   */
  public function evaluateErrorsWithCareerSubjectSchoolYear($array_filtered)
  {
    return $array_filtered;

  }

  /**
   * This method check if the students pass the year or not
   *
   * @param SchoolYear $school_year
   * @param PropelPDO $con
   */
  public function closeSchoolYear(SchoolYear $school_year, PropelPDO $con = null)
  {
    $criteria = SchoolYearStudentPeer::retrieveStudentsForSchoolYearCriteria($school_year);

    $pager = new sfPropelPager('Student', 100);
    $pager->setCriteria($criteria);
    $pager->init();
    $last_page = $pager->getLastPage();

    for ($i = 1; $i <= $last_page; $i++)
    {
      $pager->setPage($i);
      $pager->init();
      $students = $pager->getResults();

      foreach ($students as $student)
      {
		  if ($student->getLastStudentCareerSchoolYear()->getStatus() != StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE)
		  {
			  $this->stepToNextYear($student, $school_year, $con);
		  }
        
      }

      $school_year->setIsClosed(true);
      $school_year->save($con);

      StudentPeer::clearInstancePool();
    }

  }

  public function getAverage($course_subject_student, $course_subject_student_examination)
  {
    return (string) (($course_subject_student->getMarksAverage() + $course_subject_student_examination->getMark()) / 2);
  }

  public function getAnualAverageForStudentCareerSchoolYear($student_career_school_year)
  {
    if ($this->hasApprovedAllCourseSubjects($student_career_school_year))
    {
      $sum = 0;

      $course_subject_students = CourseSubjectStudentPeer::retrieveAverageableByCareerSchoolYearAndStudent(
        $student_career_school_year->getCareerSchoolYear(),
        $student_career_school_year->getStudent());

      foreach ($course_subject_students as $course_subject_student)
      {
        $sum += $course_subject_student->getFinalMark();
      }

      if (count($course_subject_students))
      {
        return round(($sum / count($course_subject_students)), 2);
      }
    }
    return null;

  }

  public function hasApprovedAllCourseSubjects($student_career_school_year)
  {
    #Counts the subjects the student did
    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student_career_school_year->getStudentId());
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $student_career_school_year->getCareerSchoolYearId());

    #Counts the subjects approved by the student during this year
    $course_subject_students = CourseSubjectStudentPeer::doSelect($c);
    /* @var $course_subject_student CourseSubjectStudent */
    foreach ($course_subject_students as $course_subject_student)
    {
      if(!$course_subject_student->getIsNotAverageable())
      {
        $course_result = $course_subject_student->getCourseResult();
        if (is_null($course_result))
        {
          return false;
        }

        if (!$course_result->isApproved())
          return false;
      }

    }

    return true;
  }

  public function nextCourseSubjectStudentExamination($course_subject_student_examination, $con)
  {
    $new_course_subject_student_examination = $course_subject_student_examination->copy();
    $new_course_subject_student_examination->setExaminationNumber($course_subject_student_examination->getExaminationNumber() + 1);
    $new_course_subject_student_examination->setMark(null);
    $new_course_subject_student_examination->setExaminationSubjectId(null);
    $new_course_subject_student_examination->setIsAbsent(false);
    $new_course_subject_student_examination->save($con);

  }

  public function createStudentApprovedCourseSubject($course_subject_student, $average, $con)
  {
    $school_year = $course_subject_student->getCourseSubject($con)->getCourse($con)->getSchoolYear($con);
    $student_approved_course_subject = new StudentApprovedCourseSubject();
    $student_approved_course_subject->setCourseSubject($course_subject_student->getCourseSubject($con));
    $student_approved_course_subject->setStudent($course_subject_student->getStudent($con));
    $student_approved_course_subject->setSchoolYear($school_year);
    $student_approved_course_subject->setMark($average);
    $course_subject_student->setStudentApprovedCourseSubject($student_approved_course_subject);

    return $student_approved_course_subject;

  }

  public function getExcludeRepprovedSubjects()
  {
    return array();

  }

  public function setFebruaryApprovedResult(StudentApprovedCareerSubject $result, $average, $examination_mark)
  {
    $result->setMark($average);

  }

  public function getExaminationNote()
  {
    return self::EXAMINATION_NOTE;
  }

  /**
   * Get the minimum allowed mark for course grading.
   *
   * @return float
   */
  public function getMinimumMark()
  {
    return self::MINIMUN_MARK;
  }

  /**
   * Get the maximum allowed mark for course grading.
   *
   * @return float
   */
  public function getMaximumMark()
  {
    return self::MAXIMUN_MARK;
  }

  public function getPosponedNote()
  {
    return self::POSTPONED_NOTE;
  }


  public function getPromotionNote()
  {
    return self::PROMOTION_NOTE;
  }

  public function getMinNote()
  {
    return self::MIN_NOTE;
  }


  public function getColorForCourseSubjectStudentMark(CourseSubjectStudentMark $course_subject_student_mark)
  {
    if (! $course_subject_student_mark->getIsClosed() || is_null($course_subject_student_mark->getMark()))
    {
      return '';
    }

    if ($course_subject_student_mark->getMark() >= $this->getPromotionNote())
    {
      $class = 'mark_green';
    }
    elseif ($course_subject_student_mark->getMark() >= $this->getMinNote())
    {
      $class = 'mark_yellow';
    }
    else
    {
      $class = 'mark_red';
    }

    return $class;
  }

    public function getExemptString()
    {
     return self::EXEMPT;
    }

    public function getFebruaryExaminationNumber()
    {
     return self::FEBRUARY;
    }

  public function getPathwayPromotionNote()
  {
       return self::PATHWAY_PROMOTION_NOTE;
  }
    
  public function canPrintWithdrawnCertificate($student)
  {
    if(!is_null($student->getLastStudentCareerSchoolYear()))
    {
            return ($student->getLastStudentCareerSchoolYear()->getStatus() == StudentCareerSchoolYearStatus::WITHDRAWN);
    }
    return false;
  }
  
  public function canPrintGraduateCertificate($student)
  {
      if(!is_null($student->getCareerStudent())){
		 return $student->getCareerStudent()->getStatus() == CareerStudentStatus::GRADUATE; 
	  }
	  return false;
  }
  
  public function getLastStudentCareerSchoolYearCoursed($student)
  {  $last_scsy =  $student->getLastStudentCareerSchoolYear();
    
    $c = new Criteria();
    $c->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);    
    $c->addJoin(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->add(StudentCareerSchoolYearPeer::STUDENT_ID,$student->getId());
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
    $c->add(CourseSubjectStudentMarkPeer::MARK,NULL, Criteria::NOT_EQUAL);
 /*$criterion = $c->getNewCriterion(CourseSubjectStudentPeer::NOT_AVERAGEABLE_CALIFICATION,NULL, Criteria::NOT_EQUAL);
      $c->addOr($criterion); */
 $c->addAnd(CourseSubjectStudentMarkPeer::IS_FREE,FALSE);
    $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::CREATED_AT);
    $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::YEAR);
    
    $scsy_c= StudentCareerSchoolYearPeer::doSelectOne($c);
    if(!is_null($scsy_c) && $last_scsy->getCareerSchoolYear()->getSchoolYear()->getYear() == $scsy_c->getCareerSchoolYear()->getSchoolYear()->getYear())
    {
        return $scsy_c;
    }
    elseif(!is_null($scsy_c) &&  $last_scsy->getCareerSchoolYear()->getSchoolYear()->getYear() > $scsy_c->getCareerSchoolYear()->getSchoolYear()->getYear() )
    {//si el ultimo año registrado es mayor al cursado me fijo si el último es 2020
        
        if($last_scsy->getCareerSchoolYear()->getSchoolYear()->getYear() == 2020)
        {
            
            $c = new Criteria();
            $c->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);    
            $c->addJoin(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::ID);
            $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
            $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
            $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
            $c->addJoin(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
            $c->add(StudentCareerSchoolYearPeer::STUDENT_ID,$student->getId());
            $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
            $c->add(CourseSubjectStudentPeer::NOT_AVERAGEABLE_CALIFICATION,NULL, Criteria::NOT_EQUAL);  
            $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::CREATED_AT);
            $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::YEAR);

            $scsy_2 = StudentCareerSchoolYearPeer::doSelectOne($c);
            
            if(!is_null($scsy_2) && $scsy_2->getCareerSchoolYear()->getSchoolYear()->getYear() == $last_scsy->getCareerSchoolYear()->getSchoolYear()->getYear() )
            {
                
                return $scsy_2;
            }
            else
            {
               return  $scsy_c;
            }
            
        }
        else
        {
            return $scsy_c;
        }
        
    }else
    {
       if(!is_null($scsy_c))
        return $scsy_c;
       else
       {
            //tiene ultimo año, pero no tiene ultimo año cursado. Es por las notas no promediables.
          $c = new Criteria();
            $c->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);    
            $c->addJoin(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::ID);
            $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
            $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
            $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
            $c->addJoin(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
            $c->add(StudentCareerSchoolYearPeer::STUDENT_ID,$student->getId());
            $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
            $c->add(CourseSubjectStudentPeer::NOT_AVERAGEABLE_CALIFICATION,NULL, Criteria::NOT_EQUAL);  
            $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::CREATED_AT);
            $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::YEAR);

            $scsy_2 = StudentCareerSchoolYearPeer::doSelectOne($c);
            return $scsy_2;

       }
    }

/*
    $c = new Criteria();
    $c->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);    
    $c->addJoin(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->add(StudentCareerSchoolYearPeer::STUDENT_ID,$student->getId());
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
    $c->add(CourseSubjectStudentMarkPeer::MARK,NULL, Criteria::NOT_EQUAL);
$c->addAnd(CourseSubjectStudentMarkPeer::IS_FREE,FALSE);
 $criterion = $c->getNewCriterion(CourseSubjectStudentPeer::NOT_AVERAGEABLE_CALIFICATION,NULL, Criteria::NOT_EQUAL);
      $c->addOr($criterion); */
/* $c->addAnd(CourseSubjectStudentMarkPeer::IS_FREE,FALSE);*/
    /*$c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::CREATED_AT);
    $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::YEAR);
    return StudentCareerSchoolYearPeer::doSelectOne($c);*/
  }

  public function getAnualAverageWithDisapprovedSubjects($student_career_school_year)
  {
        $sum = 0;

        $course_subject_students = CourseSubjectStudentPeer::retrieveAverageableByCareerSchoolYearAndStudent(
                $student_career_school_year->getCareerSchoolYear(),$student_career_school_year->getStudent());

        foreach ($course_subject_students as $course_subject_student)
        {
          $sum += $course_subject_student->getFinalMark();
        }

        if (count($course_subject_students))
        {
          return round(($sum / count($course_subject_students)), 2);
        }
        unset ($course_subject_students);
        
        return null;

    }
    
  public function canPrintRegularCertificate($student)
  {
      return ($student->getIsRegistered() && $student->getPerson()->getIsActive());
  }
  
  public function getApprovedResult()
  {
      return self::APPROVED;
  }
  
  public function getDisapprovedResult()
  {
      return self::DISAPPROVED;
  }
  
  public function getAbsentResult()
  {
      return self::ABSENT;
  }
  public function getResultStringFor($key)
  {
    return (!is_null($key) && isset($key))? $this->_result_string[$key] : '';

  }

}
