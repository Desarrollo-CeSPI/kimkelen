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

/**
 * Copy and rename this class if you want to extend and customize
 */
class LvmEvaluatorBehaviour extends BaseEvaluatorBehaviour
{

  const HISTORIA_DEL_ARTE = 134;
  const ORIENTACION_ESCOLAR = 238 ;

  protected $_introduccion = array(28, 29, 30);
  protected
  $_examination_number = array(
    self::DECEMBER => 'Regular',
    self::FEBRUARY => 'Complementario',
  );

  /*
   * Returns if a student has approved or not the course subject
   *
   * In LVM a student of first year, if dissaproved only goes to december not to february.
   * If a student is free in some period it always repproves the course_subject
   *
   * @param CourseSubjectStudent $course_subject_student
   * @param PropelPDO $con
   *
   * @return Object $object
   */

  public function getCourseSubjectStudentResult(CourseSubjectStudent $course_subject_student, PropelPDO $con = null)
  {
    $average = $course_subject_student->getMarksAverage($con);
    $sum_marks = 0;
    $year = $course_subject_student->getCourseSubject()->getCourse()->getYear();

    foreach ($course_subject_student->getCourseSubjectStudentMarks() as $cssm)
    {
      $sum_marks += $cssm->getMark();
    }

    if(($year == 5 || $year == 6) && CourseType::BIMESTER == $course_subject_student->getCourseSubject()->getCourseType())
    {
        $min_note = self::EXAMINATION_NOTE;
    }else
    {
        $min_note = self::POSTPONED_NOTE;
    }
    if (
      ($average >= $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getConfiguration()->getCourseMinimunMark()
      && $course_subject_student->getMarkFor($course_subject_student->countCourseSubjectStudentMarks())->getMark() >= $min_note)
      || (
      $year > 1
      && $year < 5
      && $sum_marks >= 21
      && $course_subject_student->getMarkFor($course_subject_student->countCourseSubjectStudentMarks())->getMark() >= 4))
    {

      $school_year = $course_subject_student->getCourseSubject()->getCourse()->getSchoolYear();

      $student_approved_course_subject = new StudentApprovedCourseSubject();
      $student_approved_course_subject->setCourseSubject($course_subject_student->getCourseSubject());
      $student_approved_course_subject->setStudent($course_subject_student->getStudent());
      $student_approved_course_subject->setSchoolYear($school_year);
      $student_approved_course_subject->setMark($average);
      $course_subject_student->setStudentApprovedCourseSubject($student_approved_course_subject);

      ###Liberando memoria ####
      $school_year->clearAllReferences(true);
      unset($school_year);
      SchoolYearPeer::clearInstancePool();
      unset($average);
      unset($sum_marks);
      ##########################
      //$student_approved_course_subject->save();
      return $student_approved_course_subject;
    }
    else
    {
      $school_year = $course_subject_student->getCourseSubject()->getCourse()->getSchoolYear();
      $career_school_year = CareerSchoolYearPeer::retrieveBySchoolYear(null, $school_year);
      
	    if ($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubject()->getIsOption() && $year == 6) {
               
	    $student_disapproved_course_subject = new StudentDisapprovedCourseSubject();
		  $student_disapproved_course_subject->setExaminationNumber($this->getExaminationNumberFor($average));
	    $student_disapproved_course_subject->setCourseSubjectStudent($course_subject_student);


	    unset($average);
	    unset($sum_marks);

	    return $student_disapproved_course_subject;


        }


      elseif ($course_subject_student->getStudent()->isFree(null, null, $career_school_year[0])) {
        if (is_null($student_repproved_course_subject = StudentRepprovedCourseSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student))){ 
          $student_repproved_course_subject = new StudentRepprovedCourseSubject();
          $student_repproved_course_subject->setCourseSubjectStudent($course_subject_student);
        }

        return $student_repproved_course_subject;
      }
      else {
        $student_disapproved_course_subject = new StudentDisapprovedCourseSubject();
        $student_disapproved_course_subject->setCourseSubjectStudent($course_subject_student);
        // si un alumno es de primer año, no puede ir a febrero siempre va a diciembre.
        if ($year == 1)
        {
          $student_disapproved_course_subject->setExaminationNumber(self::DECEMBER);
        }
        //Segundo a cuarto año
        elseif ($year > 1 && $year < 5 && $course_subject_student->countCourseSubjectStudentMarks() == 3)
        {
          //Suma menor a 21 pero mayor o igual que 12: mesa de diciembre (examen regular)
          if (($sum_marks < 21 && $sum_marks >= 12))
          {
            $student_disapproved_course_subject->setExaminationNumber(self::DECEMBER);
          }
          //Suma igual o mayor a 21 pero nota del tercer termino menor a 4: mesa de diciembre (examen regular)
          //$course_subject_student->countCourseSubjectStudentMarks() = 3
          elseif (
            $sum_marks >= 21 &&
            $course_subject_student->getMarkFor($course_subject_student->countCourseSubjectStudentMarks())->getMark() < 4)
          {
            $student_disapproved_course_subject->setExaminationNumber(self::DECEMBER);
          }
          //Suma menor a 12: mesa de marzo (examen complementario)
          elseif ($sum_marks < 12)
          {
            $student_disapproved_course_subject->setExaminationNumber(self::FEBRUARY);
          }
        }
        else
        {
          $student_disapproved_course_subject->setExaminationNumber($this->getExaminationNumberFor($average));
        }
        unset($average);
        unset($sum_marks);

        //$student_disapproved_course_subject->save();
        return $student_disapproved_course_subject;
      }
    }
  }

  public function getCurrentHistoriaDelArte()
  {
    return $this->getHistoriaDelArteForSchoolYear(SchoolYearPeer::retrieveCurrent());

  }

  public function getHistoriaDelArteForSchoolYear($school_year)
  {
    $c = new Criteria();
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, self::HISTORIA_DEL_ARTE);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID, Criteria::INNER_JOIN);
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());

    return CareerSubjectSchoolYearPeer::doSelectOne($c);

  }

  public function getLvmSpecialSubjects($school_year = null)
  {
    if (is_null($school_year))
    {
      $school_year = SchoolYearPeer::retrieveCurrent();
    }

    $c = new Criteria();
    $c->add(CareerSubjectPeer::SUBJECT_ID, $this->_introduccion, Criteria::IN);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID, Criteria::INNER_JOIN);
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());

    return CareerSubjectSchoolYearPeer::doSelect($c);

  }

  public function getCourseSubjectStudentsForIntroduccion($student, $career_school_year)
  {
    $ids = $this->getLvmSpecialSubjectIds($career_school_year->getSchoolYear());
    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
    $c->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID);
    $c->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $ids, Criteria::IN);

    return CourseSubjectStudentPeer::doSelect($c);

  }

  public function getLvmSpecialSubjectIds($school_year)
  {
    $result = array();
    foreach ($this->getLvmSpecialSubjects($school_year) as $career_subject_school_year)
    {
      $result[] = $career_subject_school_year->getId();
    }
    return $result;

  }

  /**
   * This method is a hack for a special subject in LVM behavior (Historia del arte)
   */
  public function evaluateErrorsWithCareerSubjectSchoolYear($array_filtered)
  {
    $array_result = array();
    $career_subject_school_year = $this->getCurrentHistoriaDelArte();

    foreach ($array_filtered as $k => $v)
    {
      if ($k != $career_subject_school_year->getId())
      {
        $array_result[$k] = $v;
      }
    }

    unset($career_subject_school_year);
    unset($array_filtered);

    return $array_result;

  }

  public function evaluateCareerSchoolYearStudent(CareerSchoolYear $career_school_year, Student $student, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;
    // obtenemos mas materias de este año
    #$course_subject_students = CourseSubjectStudentPeer::retrieveByCareerSchoolYearAndStudent($career_school_year, $student, $con);
    //$c = CourseSubjectStudentPeer::retrieveCriteriaByCareerSchoolYearAndStudent($career_school_year, $student, $con);
    //$c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID, Criteria::INNER_JOIN);
    //$c->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $this->getCurrentHistoriaDelArte()->getId(), Criteria::NOT_EQUAL);

    // para todas las materias cursadas este año
    //$course_subject_students = CourseSubjectStudentPeer::doSelect($c);

    $school_year = SchoolYearPeer::retrieveCurrent();

    $course_subject_students = $student->getCourseSubjectStudentsForSchoolYear($school_year);
    CourseSubjectStudentPeer::clearInstancePool();
    unset($school_year);

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
        ###Liberando memoria ###
        $result->clearAllReferences(true);
        $log->clearAllReferences(true);
        unset($result);
      }
      $course_subject_student->clearAllReferences(true);
      unset($course_subject_student);
      ###########################
    }

    ###Liberando memoria ###
    unset($course_subject_students);
    StudentApprovedCourseSubjectPeer::clearInstancePool();
    StudentDisapprovedCourseSubjectPeer::clearInstancePool();
    ################################
    if ($this->getCurrentHistoriaDelArte())
    {
      $this->evaluateHistoriaDelArteCareerSchoolYearStudent($career_school_year, $student, $con);
    }
  }

  private function evaluateHistoriaDelArteCareerSchoolYearStudent(CareerSchoolYear $career_school_year, Student $student, PropelPDO $con)
  {
    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID, Criteria::INNER_JOIN);
    $c->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $this->getCurrentHistoriaDelArte()->getId());

    $course_subject_students = CourseSubjectStudentPeer::doSelect($c);


    if (empty($course_subject_students) || count($course_subject_students) == 1)
      return false;

    $course_result_1 = $course_subject_students[0]->getCourseResult();
    $course_result_2 = $course_subject_students[1]->getCourseResult();

    if (is_null($course_result_1) || is_null($course_result_2))
      return false;

    //APROBO LAS DOS
    if ($course_result_1 instanceof StudentApprovedCourseSubject && $course_result_2 instanceof StudentApprovedCourseSubject)
    {
      $student_approved_career_subject = new StudentApprovedCareerSubject();
      $student_approved_career_subject->setCareerSubject($course_result_1->getCourseSubject($con)->getCareerSubject($con));
      $student_approved_career_subject->setStudent($student);
      $student_approved_career_subject->setSchoolYear($course_result_1->getSchoolYear($con));

      $avg = ($course_result_1->getMark() + $course_result_2->getMark()) / 2;
      $avg = sprintf('%.4s', $avg);
      $student_approved_career_subject->setMark($avg);

      $course_result_1->setStudentApprovedCareerSubject($student_approved_career_subject);
      $course_result_2->setStudentApprovedCareerSubject($student_approved_career_subject);

      $student_approved_career_subject->save($con);
      $course_result_1->save($con);
      $course_result_2->save($con);

      ###liberando memoria###
      $student_approved_career_subject->clearAllReferences(true);
      unset($student_approved_career_subject);
      unset($course_result_1);
      unset($course_result_2);
      ########################
    }
    else
    {
      if ($course_result_1 instanceof StudentDisapprovedCourseSubject && $course_result_2 instanceof StudentDisapprovedCourseSubject)
      {
        $course_subject_student = ($course_result_1->getExaminationNumber() > $course_result_2->getExaminationNumber()) ? $course_subject_students[0] : $course_subject_students[1];
        $this->createCourseSubjectStudentExamination($course_subject_student, $con);
      }
      elseif ($course_result_1 instanceof StudentDisapprovedCourseSubject)
      {
        $this->createCourseSubjectStudentExamination($course_subject_students[0], $con);
      }
      elseif ($course_result_2 instanceof StudentDisapprovedCourseSubject)
      {
        $this->createCourseSubjectStudentExamination($course_subject_students[1], $con);
      }
    }
    unset($course_subject_students);

  }

  public function closeCourseSubjectStudentExamination(CourseSubjectStudentExamination $course_subject_student_examination, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    $course_subject_student = $course_subject_student_examination->getCourseSubjectStudent();

    // si aprueba la mesa de examen
    if ($course_subject_student_examination->getMark() >= self::EXAMINATION_NOTE)
    {

      $result = StudentApprovedCareerSubjectPeer::retrieveOrCreateByCareerSubjectAndStudent($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubject()->getId(), $course_subject_student->getStudent()->getId());

      $result->setCareerSubject($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubject());
      $result->setStudent($course_subject_student->getStudent());
      $result->setSchoolYear($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear());
      if (in_array($course_subject_student->getCourseSubject()->getSubject()->getId(), $this->_introduccion))
      {
        $average = (string) $course_subject_student_examination->getMark();
      }
      else
      {
        $average = (string) (($course_subject_student->getMarksAverage() + $course_subject_student_examination->getMark()) / 2);
      }

      $average = sprintf('%.4s', $average);
      // se guarda la NOTA FINAL de la materia
      if ($this->getCurrentHistoriaDelArte() && $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYearId() == $this->getCurrentHistoriaDelArte()->getId())
      {
        $average = $course_subject_student_examination->getMark();
      }

      if ($average < 4)
      {
        $average = 4;
      }
      $result->setMark($average);

      ##se agrega en la tupla student_disapproved_course_subject el link a al resultado final

      $student_disapproved_course_subject = $course_subject_student->getCourseResult();
      $student_disapproved_course_subject->setStudentApprovedCareerSubject($result);
      $student_disapproved_course_subject->save($con);

      $result->save($con);

      //Se busca si había una previa creada para esta materia, se debe eliminar

      if ($student_repproved_course_subject = StudentRepprovedCourseSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student))
      {
        $student_repproved_course_subject->delete($con);
      }
    }
    else
    {
      // TODO: arreglar esto: pedir a la configuración
      // Pasa de diciembre a febrero (se copia el course_subject_student_examination con examination_number + 1)
      if ($course_subject_student_examination->getExaminationNumber() < count($this->_examination_number))
      {
        $new_course_subject_student_examination = $course_subject_student_examination->copy();
        $new_course_subject_student_examination->setExaminationNumber($course_subject_student_examination->getExaminationNumber() + 1);
        $new_course_subject_student_examination->setMark(null);
        $new_course_subject_student_examination->setExaminationSubjectId(null);
        $new_course_subject_student_examination->setIsAbsent(false);
        $new_course_subject_student_examination->save($con);
      }
      else
      {
        // se crea una previa
        $student_repproved_course_subject = StudentRepprovedCourseSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student);

        if (is_null($student_repproved_course_subject))
        {
          $student_repproved_course_subject = new StudentRepprovedCourseSubject();
        }

        $student_repproved_course_subject->setCourseSubjectStudentId($course_subject_student->getId());
        $student_repproved_course_subject->save($con);
      }
    }
  }

  public function getAnualAverageForStudentCareerSchoolYear($student_career_school_year)
  {
    if ($this->hasApprovedAllCourseSubjects($student_career_school_year))
    {
      $c = new Criteria();
      $c->add(StudentApprovedCareerSubjectPeer::STUDENT_ID, $student_career_school_year->getStudentId());
      $c->addJoin(StudentApprovedCareerSubjectPeer::CAREER_SUBJECT_ID,  CareerSubjectPeer::ID);
      $c->addJoin(StudentApprovedCareerSubjectPeer::SCHOOL_YEAR_ID, SchoolYearPeer::ID);
      $c->add(CareerSubjectPeer::YEAR,$student_career_school_year->getYear());
      $c->add(SchoolYearPeer::YEAR, $student_career_school_year->getCareerSchoolYear()->getSchoolYear()->getYear(), Criteria::GREATER_EQUAL);  
        
      $student_approved_career_subjects = StudentApprovedCareerSubjectPeer::doSelect($c);
      if ($student_career_school_year->getYear() == 4)
      {
        $sum = 0;
        $sum_introduccion = 0;
        foreach ($student_approved_career_subjects as $student_approved_career_subject)
        {
          if (in_array($student_approved_career_subject->getCareerSubject()->getSubject()->getId(), $this->_introduccion))
          {
            $sum_introduccion += $student_approved_career_subject->getMark();
          }
          else
          {
            $sum += $student_approved_career_subject->getMark();
          }
        }
        $sum += $sum_introduccion / 3;
        $count = count($student_approved_career_subjects) - 2;
        
      }
      elseif ($student_career_school_year->getYear() == 6)
      {
        $sum = 0;
        foreach ($student_approved_career_subjects as $student_approved_career_subject)
        {
          $is_historia = self::HISTORIA_DEL_ARTE == $student_approved_career_subject->getCareerSubject()->getSubjectId()
          || in_array($student_approved_career_subject->getCareerSubjectid(), array(261,262));
          if ($is_historia)
          {
            $historia_mark = $this->getHistoriaDelArteMark($student_approved_career_subject->getStudent(), $student_approved_career_subject->getSchoolYear());
          }
          else
          {
            $sum += $student_approved_career_subject->getMark();
          }
        }
        if (isset($historia_mark)){
          $sum += $historia_mark;
          $count = count($student_approved_career_subjects) - 1;
        }
        else {
          $count = count($student_approved_career_subjects);
        }
      }
      else
     {   $sum = 0;
         $cant = 0;
        foreach ($student_approved_career_subjects as $student_approved_career_subject)
        {
            if($student_approved_career_subject->getCareerSubject()->getId() != self::ORIENTACION_ESCOLAR)
            {
               $sum += $student_approved_career_subject->getMark();
               $cant ++;
            }
           
        }
       
        $count = $cant;

      }
      
      if ($sum > 0 && $count > 0)
      {
        return number_format(round(($sum / $count), 2), 2, '.', '');
      }
    }
    return null;
       
  }

  public function hasApprovedAllCourseSubjects($student_career_school_year)
  {

    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student_career_school_year->getStudentId());
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $student_career_school_year->getCareerSchoolYearId());

    $course_subject_students = CourseSubjectStudentPeer::doSelect($c);

    foreach($course_subject_students as $course_subject_student){
       if (is_null($course_subject_student->getStudentApprovedCareerSubject()))
         return false;
    }

    return true;
  }

  //This function is a hack for Historia del arte
  public function getHistoriaDelArteMark($student, $school_year)
  {
    $historia = array(self::HISTORIA_DEL_ARTE, 261, 262);
    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, $historia, Criteria::IN);
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());

    $avg = 0;
    foreach (CourseSubjectStudentPeer::doSelect($c) as $course_subject_student)
    {
      $course_result = $course_subject_student->getCourseResult();
      if ((is_null($course_result)) || ($course_result instanceof StudentDisapprovedCourseSubject))
      {
        return '';
      }

      $avg += $course_result->getMark();
    }

    $avg = $avg / 2;

    return sprintf('%.4s', $avg);
  }

   public function getExaminationNumberFor($average, $is_free = false, $course_subject_student = null)
  {
    // return (($average >= self::MIN_NOTE)) ? self::DECEMBER : self::FEBRUARY;
       return self::DECEMBER;
  }

  /**
   * If the student approves examination repproved, then it creates a student_approved_career_subject for this student
   *
   * @param StudentExaminationRepprovedSubject $student_examination_repproved_subject
   * @param PropelPDO $con
   */
  public function closeStudentExaminationRepprovedSubject(StudentExaminationRepprovedSubject $student_examination_repproved_subject, PropelPDO $con)
  {
    if ($student_examination_repproved_subject->getMark() >= $this->getExaminationNote())
    {
      $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($student_examination_repproved_subject->getStudentRepprovedCourseSubject()->getCourseSubjectStudent());
      if (is_null($student_approved_career_subject)){
      $student_approved_career_subject = new StudentApprovedCareerSubject();
      }
      $student_approved_career_subject->setCareerSubject($student_examination_repproved_subject->getExaminationRepprovedSubject()->getCareerSubject());
      $student_approved_career_subject->setStudent($student_examination_repproved_subject->getStudent());
      $student_approved_career_subject->setSchoolYear($student_examination_repproved_subject->getExaminationRepprovedSubject()->getExaminationRepproved()->getSchoolYear());

      $examination_type = $student_examination_repproved_subject->getExaminationRepprovedSubject()->getExaminationRepproved()->getExaminationType();
      if ($examination_type == ExaminationRepprovedType::REPPROVED)
      {
        //If it is a previous examination instance, final average is the average of the course_subject_student and the mark of student_examination_repproved_subject
        $average = (string) (($student_examination_repproved_subject->getStudentRepprovedCourseSubject()->getCourseSubjectStudent()->getMarksAverage() + $student_examination_repproved_subject->getMark()) / 2);

        $average = sprintf('%.4s', $average);
        if ($average < self::MIN_NOTE)
        {
          $average = self::MIN_NOTE;
        }
      }
      else // if it's a free examination instance, mark is simply the mark gotten at the exam
      {
        $average = $student_examination_repproved_subject->getMark();
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
    elseif($result instanceof StudentDisapprovedCourseSubject)
    {	
      $c = new Criteria();
      $c->add(CourseSubjectStudentExaminationPeer::EXAMINATION_NUMBER, $result->getExaminationNumber());
      $c->add(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, $result->getCourseSubjectStudent()->getId());
      if (CourseSubjectStudentExaminationPeer::doCount($c) == 0)
      {
        $this->createCourseSubjectStudentExamination($result->getCourseSubjectStudent(null, $con), $con);
      }
    }  
    /*instance of StudentRepprovedCourseSubject*/
    /*No se crea nada ya que se debe inscribir en la mesa.*/
    
  }

    public function getAnualAverageWithDisapprovedSubjects($student_career_school_year)
    {
       
        $course_subject_students = CourseSubjectStudentPeer::retrieveAverageableByCareerSchoolYearAndStudent(
                  $student_career_school_year->getCareerSchoolYear(),$student_career_school_year->getStudent());

        /* $c = StudentApprovedCareerSubjectPeer::retrieveCriteriaForStudentCareerSchoolYear($student_career_school_year);
          $student_approved_career_subjects = StudentApprovedCareerSubjectPeer::doSelect($c);*/

        if ($student_career_school_year->getYear() == 4)
        {
            $sum = 0;
            $sum_introduccion = 0;
            foreach ($course_subject_students as $course_subject_student)
            {
              if (in_array($course_subject_student->getCourseSubject()->getSubject()->getId(), $this->_introduccion))
              {
                $sum_introduccion += $course_subject_student->getFinalMark();
              }
              else
              {
                $sum += $course_subject_student->getFinalMark();
              }
            }
            $sum += $sum_introduccion / 3;
            $count = count($course_subject_students) - 2;
        }
        elseif ($student_career_school_year->getYear() == 6)
        {
          $sum = 0;
          foreach ($course_subject_students as $course_subject_student)
          {
            $is_historia = self::HISTORIA_DEL_ARTE == $course_subject_student->getCourseSubject()->getSubject()->getId()
            || in_array($course_subject_student->getCourseSubject()->getCareerSubject()->getId(), array(261,262));

            if ($is_historia)
            {
              $historia_mark = $this->getHistoriaDelArteMark($course_subject_student->getStudent(), $course_subject_student->getCourseSubject()->getCareerSubject()->getCareerSchoolYear()->getSchoolYear());
            }
            else
            {
              $sum += $course_subject_student->getFinalMark();
            }
          }

          if (isset($historia_mark)){
            $sum += $historia_mark;
            $count = count($course_subject_students) - 1;
          }
          else {
            $count = count($course_subject_students);
          }
        }
        else
        {
          $sum=0;
          $count=0;
          foreach ($course_subject_students as $course_subject_student)
          {
            if($course_subject_student->getCourseSubject()->getCareerSubject()->getId() != self::ORIENTACION_ESCOLAR)
            {
               $sum += $course_subject_student->getFinalMark();
              $count ++;
            }  
            
          }              
        }

        if ($sum > 0 && $count > 0)
        {

          return round($sum/$count, 2);
        }
          
        unset ($course_subject_students);

    }
    
    public function canPrintGraduateCertificate($student)
    {
        if(!is_null($student->getCareerStudent()))
        {
            if ($student->getCareerStudent()->getStatus() == CareerStudentStatus::GRADUATE)
            {
                return true;
            }
            else
            {
               //chequeo que esté en 6to y tenga todas las materias aprobadas.
                $this->student_career_school_years = $student->getStudentCareerSchoolYears();
                $scsy_cursed = $student->getLastStudentCareerSchoolYearCoursed();
                
                if(is_null($scsy_cursed))
                {
                    return false;
                }

                $max_year = $scsy_cursed->getCareerSchoolYear()->getCareer()->getMaxYear();

                if($scsy_cursed->getYear() != $max_year)
                    return false;

                foreach ($this->student_career_school_years as $scsy)
                {
                    if($scsy->getStatus() == StudentCareerSchoolYearStatus::APPROVED || $scsy->getStatus() == StudentCareerSchoolYearStatus::IN_COURSE || $scsy->getStatus() == StudentCareerSchoolYearStatus::LAST_YEAR_REPPROVED
                            || $scsy->getStatus() == StudentCareerSchoolYearStatus::FREE || ($scsy->getStatus() == StudentCareerSchoolYearStatus::WITHDRAWN  && 
                             $scsy->getId() == $scsy_cursed->getId())){

                        $career_school_year = $scsy->getCareerSchoolYear();
                        $school_year = $career_school_year->getSchoolYear();

                        $csss = CourseSubjectStudentPeer::retrieveByCareerSchoolYearAndStudent($career_school_year, $student);
                        foreach ($csss as $css)
                        { 
                            if (is_null($css->getStudentApprovedCareerSubject()) && is_null($css->getStudentApprovedCourseSubject()))
                            {
                                return false;                                                
                            }
                        }
                    }
                }
                return true;
            }
        }

    return false;

    }
    
    public function canPrintWithdrawnCertificate($student)
    {   
        if ($this->canPrintGraduateCertificate($student))
            return false;
        
        $scsy = $student->getLastStudentCareerSchoolYear();
        
        if(!is_null($scsy))
        {
           if($scsy->getStatus() == StudentCareerSchoolYearStatus::FREE)
           {
               return false;
           }
           else
           {
               if(!is_null($student->getCareerStudent())){

                    return ! $student->getCareerStudent()->getStatus() == CareerStudentStatus::GRADUATE; 
                }
           }
           return false; 
        }   
    }
}
