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

class CourseSubject extends BaseCourseSubject
{

  /*
   * If the course doesn´t have students inscripted, can be deleted.
   *
   * @return boolean
   */

  public function canBeDeleted(PropelPDO $con = null)
  {
    return $this->countCourseSubjectStudents() == 0;

  }

  public function getDiffStudentsFromDivision($division, $con)
  {
    $c = new Criteria();
    if ($this->getCareerSubject()->getOrientation())
    {
      $c->add(CareerStudentPeer::ORIENTATION_ID, $this->getCareerSubject()->getOrientationId());
      $c->addJoin(CareerStudentPeer::STUDENT_ID, StudentPeer::ID);
      $c->addJoin(DivisionStudentPeer::STUDENT_ID, StudentPeer::ID);
    }

    if ($this->getCareerSubject()->getSubOrientation())
    {
      $c->add(CareerStudentPeer::SUB_ORIENTATION_ID, $this->getCareerSubject()->getSubOrientationId());
    }

    return $students = array_diff($division->getStudents($c), $this->getStudents());

  }

  /*
   * If course has a division then copy every student into this course subject. If the course has any student, only copy
   * new ones.
   *
   * @param   PropelPDO $con
   *
   */

  public function copyStudentsFromDivision(PropelPDO $con = null)
  {
    if ($this->canCopyStudentsFromDivision())
    {
      $division = $this->getCourse()->getDivision();

      if (!is_null($division))
      {
        $students = $this->getDiffStudentsFromDivision($division, $con);

        foreach ($students as $new_student)
        {
          $course_student = new CourseSubjectStudent();
          $course_student->setCourseSubject($this);
          $course_student->setStudent($new_student);
          $course_student->save($con);

          $course_student->clearAllReferences(true);
          unset($course_student);
          $new_student->clearAllReferences(true);
          unset($new_student);
        }
        unset($students);
      }
      $this->clearAllReferences(true);
    }

  }

  public function canCopyStudentsFromDivision()
  {
    if ($this->getCareerSubject()->getHasOptions() || $this->getCareerSubject()->getIsOption())
    {
      return false;
    }

    $division = $this->getCourse()->getDivision();
    if (is_null($division))
    {
      return false;
    }

    $c = new Criteria();

    if ($this->getCareerSubject()->getOrientation())
    {
      $c->add(CareerStudentPeer::ORIENTATION_ID, $this->getCareerSubject()->getOrientationId());
      $c->addJoin(CareerStudentPeer::STUDENT_ID, StudentPeer::ID);
      $c->addJoin(DivisionStudentPeer::STUDENT_ID, StudentPeer::ID);
    }

    return (count($division->getStudents($c)) > count($this->getStudents()));

  }

  public function getStudents()
  {
    $ret = array();
    foreach ($this->getCourseSubjectStudents() as $css)
    {
      if ($css->getStudent()->getPerson()->getIsActive())
      {
        $ret[] = $css->getStudent();
      }
    }
    return $ret;

  }

  public function getPathwayStudents()
  {
    $ret = array();
    foreach ($this->getCourseSubjectStudentPathways() as $css)
    {
      if ($css->getStudent()->getPerson()->getIsActive())
      {
        $ret[] = $css->getStudent();
      }
    }
    return $ret;

  }

  public function getAllMarksForStudent($student)
  {
    $criteria = new Criteria();

    $criteria->setLimit(1);
    $criteria->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());

    $course_subject_students = $this->getCourseSubjectStudents($criteria);

    $marks = array();

    foreach ($course_subject_students as $course_subject_student)
    {
      $marks = array_merge($marks, $course_subject_student->getCourseSubjectStudentMarks());
    }

    return $marks;

  }

  /**
   * this method return how many marks have this course_subject
   * @return type
   */
  public function countMarks()
  {
    return $this->getCareerSubjectSchoolYear()->getConfiguration()->getCourseMarks();

  }

  public function getCareerSubject(PropelPDO $con = null)
  {
    return $this->getCareerSubjectSchoolYear($con)->getCareerSubject($con);

  }

  /*
   * This method returns differetns strings if the course has a division or not.
  */
  public function getCareerSubjectToString()
  {
    if ($this->getCourse()->getDivision())
    {
      return $this->getCareerSubject()->__toString();
    }
    else
    {
      return $this->getCareerSubject()->toStringWithCareer();
    }

  }

  public function __toString()
  {
    return $this->getCareerSubject()->getSubject()->__toString();
  }

  /**
   * This method close all the course subjects student marks of the current period.
   * If the current period is the last one then allso creates the result of the student
   * @param PropelPDO $con
   * @param integer $current_period
   */
  public function close(PropelPDO $con, $current_period)
  {

    foreach ($this->getCourseSubjectStudents() as $course_subject_student)
    {
      $mark = $course_subject_student->getMarkFor($current_period);

      if ($mark && $mark->getMark() != '')
      {
        $mark->setIsClosed(true);
        $mark->save($con);
      }
      else
      {
        return false;
      }
    }
    unset($mark);
    #SI ES EL ULTIMO PERIODO... numero de periodo == cantidad de notas
    if ($current_period == $this->getCareerSubjectSchoolYear()->getConfiguration()->getCourseMarks())
    {
      foreach ($this->getCourseSubjectStudents() as $course_subject_student)
      {
        $result_object = $course_subject_student->getCourseResult($con);

        if (!is_null($result_object))
        {
          $result_object->save($con);
        }
        if ($result_object instanceof StudentApprovedCourseSubject)
        {
          $course_subject_student->setStudentApprovedCourseSubject($result_object);
          $course_subject_student->save($con);
        }
      }
      unset($result_object);
      unset($course_subject_student);
      return true;
    }
    return false;

  }

  public function isFinalPeriod()
  {
    //busco los retirados o con reserva de banco
    $c = new Criteria();
    $c->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
    $criterion = $c->getNewCriterion(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN, Criteria::EQUAL);
    $criterion->addOr($c->getNewCriterion(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE, Criteria::EQUAL));
    $c->add($criterion);
    $c->clearSelectColumns();
    $c->addSelectColumn(StudentPeer::ID);
    $stmt = StudentPeer::doSelectStmt($c);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);  
    
    $c = new Criteria();
    $c->add(CourseSubjectStudentMarkPeer::MARK, null, Criteria::ISNULL);
    $c->addAnd(CourseSubjectStudentMarkPeer::IS_CLOSED, false);
    $c->addJoin(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->add(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, $this->getId());
    $c->add(CourseSubjectStudentPeer::IS_NOT_AVERAGEABLE, false);
    $c->addJoin(StudentPeer::ID,CourseSubjectStudentPeer::STUDENT_ID);
    $c->addJoin(StudentPeer::PERSON_ID,PersonPeer::ID);
    $c->add(PersonPeer::IS_ACTIVE,true);
    $c->add(StudentPeer::ID, $ids, Criteria::NOT_IN);
    
    //die(var_dump(CourseSubjectStudentMarkPeer::doSelect($c)));   
    return CourseSubjectStudentMarkPeer::doCount($c) == 0;

  }

  public function hasAttendanceForSubject($con = null)
  {
    return $this->getCareerSubjectSchoolYear()->getConfiguration($con) ? $this->getCareerSubjectSchoolYear()->getConfiguration($con)->hasAttendanceForSubject() : null;

  }

  public function hasAttendanceForDay($con = null)
  {
    return !$this->hasAttendanceForSubject($con);

  }

  public function getCountStudents()
  {
    return $this->countCourseSubjectStudents();

  }

  public function getCountHours()
  {
    $hours = array(0 => '00', 1 => '00', 2 => '00');
    foreach ($this->getCourseSubjectDays() as $csd)
    {
      $ends_at = explode(':', $csd->getEndsAt());
      $starts_at = explode(':', $csd->getStartsAt());

      $hours[0] += abs($ends_at[0] - $starts_at[0]);
      $hours[1] += abs($ends_at[1] - $starts_at[1]);
      $hours[2] += abs($ends_at[2] - $starts_at[2]);

      //$hours +=
    }
    $hours = implode(':', $hours);
    return $hours;

  }

  public function getDivision()
  {
    return $this->getCourse()->getDivision();

  }

  public function getCareer()
  {
    return $this->getCareerSubject()->getCareer();

  }

  public function getSubject()
  {
    return $this->getCareerSubject()->getSubject();

  }

  public function fullToString()
  {
    return $this->__toString() . ' -- ' . $this->getCourse();

  }

  public function getCourseSubjectStudent($student_id)
  {
    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student_id);
    $c->add(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, $this->getId());
    return CourseSubjectStudentPeer::doSelectOne($c);
  }

  public function getCourseSubjectStudents($criteria = null, PropelPDO $con = null)
  {
    if ($criteria === null)
    {
      $criteria = new Criteria();
    }

     //busco los retirados o con reserva de banco
     $c = new Criteria();
     $c->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
     $criterion = $c->getNewCriterion(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN, Criteria::EQUAL);
     $criterion->addOr($c->getNewCriterion(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE, Criteria::EQUAL));
     $c->add($criterion);
     $c->clearSelectColumns();
     $c->addSelectColumn(StudentPeer::ID);
     $stmt = StudentPeer::doSelectStmt($c);
     $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
      
     $criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID);
     $criteria->add(CourseSubjectStudentPeer::IS_NOT_AVERAGEABLE, false);
     $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
     $criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentCareerSchoolYearPeer::STUDENT_ID);
     $criteria->add(StudentPeer::ID, $ids, Criteria::NOT_IN);
     $criteria->add(PersonPeer::IS_ACTIVE,TRUE);
     $criteria->setDistinct();    
     $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);

     return parent::getCourseSubjectStudents($criteria);
  }

  public function getCourseSubjectConfigurationDivisionForm()
  {
    $c = new Criteria();
    $course_type = $this->getCareerSubjectSchoolYear()->getConfiguration()->getCourseType();
    $is_bimester = ($course_type == CourseType::BIMESTER);
    $c->add(CourseSubjectConfigurationPeer::COURSE_SUBJECT_ID, $this->getId());
    $new = (CourseSubjectConfigurationPeer::doSelect($c));

    if ($is_bimester && !count($new))
    {
      $form = new CourseSubjectConfigurationFirstForm($this);
    }
    else
    {
      $form = new CourseSubjectConfigurationManyForm($this);
    }
    return $form;

  }

  public function getCourseType()
  {
    return $this->getCareerSubjectSchoolYear()->getConfiguration()->getCourseType();
  }

  /**
   * This method returns the condifuration for the period.
   *
   * @param CareerSchoolYearPeriod $period
   * @return CourseSubjectConfiguration
   */
  public function getConfigurationForPeriod(CareerSchoolYearPeriod $period)
  {
    $c = new Criteria();
    $c->add(CourseSubjectConfigurationPeer::COURSE_SUBJECT_ID, $this->getId());
    $c->add(CourseSubjectConfigurationPeer::CAREER_SCHOOL_YEAR_PERIOD_ID, $period->getId());

    return CourseSubjectConfigurationPeer::doSelectOne($c);
  }

  /**
   * If exist the configuration then returns the max absence for the period.
   * @see getConfigurationForPeriod
   * @param CareerSchoolYearPeriod $period
   *
   * @return integer
   */
  public function getMaxAbsenceForPeriod(CareerSchoolYearPeriod $period = null)
  {
    if ($this->getIsAbsenceForPeriod() && (!is_null($period)))
    {

      if (!is_null($configuration = $this->getConfigurationForPeriod($period)))
      {

        $max_absence = $configuration->getMaxAbsence();
        if (!is_null($max_absence))
        {
          return $max_absence;
        }
      }
      return $period->getMaxAbsences();
    }

    return $this->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getMaxAbsenceInYear($this->getYear());

  }

  public function getIsAbsenceForPeriod()
  {
    return $this->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getIsAbsenceForPeriodInYear($this->getYear());
  }

  public function updateCourseMarks($cant_marks, $con = null)
  {
    if ($con == null)
    {
      $con = Propel::getConnection(StudentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    $con->beginTransaction();
    try
    {
      $course_subject_students = CourseSubjectStudentPeer::retrieveByCourseSubject($this->getId());

      foreach ($course_subject_students as $course_subject_student)
      {
        $course_subject_student->updateCourseMarks($cant_marks, $con);
      }

      $this->getCourse()->setCurrentPeriod(1);
      $this->getCourse()->setIsClosed(false);
      $this->getCourse()->save($con);
      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

  public function deleteCourseSubjectConfiguration()
  {
    foreach (CourseSubjectConfigurationPeer::retrieveBySubject($this) as $csc)
    {
      $csc->delete();
    }
  }

  public function deleteStudentAttendances()
  {
    $student_attendances = array();
    foreach (StudentAttendancePeer::retrieveBySubject($this) as $student_attendance)
    {
      StudentAttendanceJustificationPeer::doDelete($student_attendance->getStudentAttendanceJustificationId());
      $student_attendance->delete();
    }

  }

  public function copyTeachersToCourseSubject(PropelPDO $con = null, $copy_course_subject)
  {
    $course_subject_teachers = $this->getCourseSubjectTeachers();

    foreach ($course_subject_teachers as $course_subject_teacher)
    {
      $new_course_subject_teacher = new CourseSubjectTeacher();
      $new_course_subject_teacher->setCourseSubject($copy_course_subject);
      $teacher = $course_subject_teacher->getTeacher();
      $new_course_subject_teacher->setTeacher($teacher);
      $new_course_subject_teacher->save($con);

      $teacher->clearAllReferences(true);
      unset($teacher);
      $new_course_subject_teacher->clearAllReferences(true);
      unset($new_course_subject_teacher);
      $course_subject_teacher->clearAllReferences(true);
      unset($course_subject_teacher);
    }
    unset($course_subject_teachers);
    $this->clearAllReferences(true);

  }

  public function copyCourseSubjectDays(PropelPDO $con = null, $copy_course_subject)
  {
    $course_subject_days = $this->getCourseSubjectDays();

    foreach ($course_subject_days as $course_subject_day)
    {
      $new_course_subject_day = $course_subject_day->copy();
      $new_course_subject_day->setCourseSubject($copy_course_subject);
      $new_course_subject_day->save($con);

      $new_course_subject_day->clearAllReferences(true);
      unset($new_course_subject_day);
      $course_subject_day->clearAllReferences(true);
      unset($course_subject_day);
    }
    unset($course_subject_days);
    $this->clearAllReferences(true);

  }

  public function createFor($student, PropelPDO $con = null)
  {
    $course_student = new CourseSubjectStudent();
    $course_student->setCourseSubject($this);
    $course_student->setStudent($student);
    $course_student->save($con);

    $course_student->clearAllReferences();
    unset($course_student);

  }

  public function isConfiguredToCourse($day_number)
  {
    $c = new Criteria();
    $c->add(CourseSubjectDayPeer::COURSE_SUBJECT_ID, $this->getId());
    $c->add(CourseSubjectDayPeer::DAY, $day_number);

    return CourseSubjectDayPeer::doCount($c) > 0;

  }

  public function hasAttendanceForDate($date)
  {
    $c = new Criteria();
    $c->add(StudentAttendancePeer::COURSE_SUBJECT_ID, $this->getId());
    $c->add(StudentAttendancePeer::DAY, $date);

    return StudentAttendancePeer::doCount($c) > 0;

  }

  public function getCareerSchoolYear()
  {
    return $this->getCareerSubjectSchoolYear()->getCareerSchoolYear();
  }

  public function getCareerSchoolYearPeriods()
  {
    if ($this->hasAttendanceForSubject())
    {	
      $c = new Criteria();
      $c->addJoin(CareerSchoolYearPeriodPeer::ID, CourseSubjectConfigurationPeer::CAREER_SCHOOL_YEAR_PERIOD_ID);
      $c->add(CourseSubjectConfigurationPeer::COURSE_SUBJECT_ID, $this->getId());
      $c->addAscendingOrderByColumn(CareerSchoolYearPeriodPeer::COURSE_TYPE);
      $c->addAscendingOrderByColumn(CareerSchoolYearPeriodPeer::START_AT);

      $periods = CareerSchoolYearPeriodPeer::doSelect($c);
   
      if(count($periods) == 0){
		$periods = $this->getCareerSchoolYear()->getCareerSchoolYearPeriodsForYearAndCourseType($this->getYear(), $this->getCourseType());
	  }
	 
      return $periods;
    }
    else
    {
      $periods = $this->getCareerSchoolYear()->getCareerSchoolYearPeriodsForYearAndCourseType($this->getYear(), $this->getCourseType());

      return $periods;
    }

  }

  public function getLastCareerSchoolYearPeriod()
  {
    $periods = $this->getCareerSchoolYearPeriods();

    return array_pop($periods);

  }

  public function getFirstCareerSchoolYearPeriod()
  {
    $periods = $this->getCareerSchoolYearPeriods();

    return array_shift($periods);

  }

  public function getYear()
  {
    return $this->getCareerSubjectSchoolYear()->getCareerSubject()->getYear();

  }

  public function isInPeriod($day)
  {
    $initial_period = $this->getFirstCareerSchoolYearPeriod();
    $final_period = $this->getLastCareerSchoolYearPeriod();
    if (is_null($final_period))
      if (is_null($final_period))
      {
        return true;
      }

    $initial = strtotime($initial_period->getStartAt());
    $final = strtotime($final_period->getEndAt());
    $day = strtotime($day);

    if ($day >= $initial && $day <= $final)
    {
      return true;
    }
    return false;

  }

  public function getQuaterlyPeriod()
  {

    $this->getCareerSubjectSchoolYear()->getConfiguration()->getCourseType();
    foreach ($this->getCourseSubjectConfigurations() as $config)
    {
      return $config->getPeriod()->getCareerSchoolYearPeriodId();
    }
  }

  /*
  * This method returns the average for the mark number.
  */
  public function getAverageForMarkNumber($mark_number = null)
  {
    if ($mark_number < $this->getCourse()->getCurrentPeriod() || is_null($mark_number) && $this->getCourse()->getIsClosed())
    {
      $c = new Criteria();
      $c->add(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, $this->getId());
      $c->addJoin(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);

      if (! is_null($mark_number))
      {
        $c->add(CourseSubjectStudentMarkPeer::MARK_NUMBER, $mark_number);
      }


      $c->clearSelectColumns();
      $c->addSelectColumn("SUM(" . CourseSubjectStudentMarkPeer::MARK . ") as sum");
      $stmt = CourseSubjectStudentMarkPeer::doSelectStmt($c);
      $sum = $stmt->fetchAll(PDO::FETCH_COLUMN);

      return sprintf('%.4s', ($sum[0] /  CourseSubjectStudentMarkPeer::doCount($c)));
    }
  }

  public function countCourseSubjectStudentExaminationForExaminationNumber($examination_number)
  {
    $c = CourseSubjectStudentExaminationPeer::retrieveCriteriaForCourseSubjectAndExaminationNumber($this, $examination_number);

    return CourseSubjectStudentExaminationPeer::doCount($c);
  }

  public function countCourseSubjectStudentExaminationApprovedForExaminationNumber($examination_number)
  {
    $c = CourseSubjectStudentExaminationPeer::retrieveCriteriaForCourseSubjectAndExaminationNumber($this, $examination_number);

    $criterion = $c->getNewCriterion(CourseSubjectStudentExaminationPeer::MARK,  SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationNote() ,Criteria::LESS_THAN);
    $criterion->addOr($c->getNewCriterion(CourseSubjectStudentExaminationPeer::MARK,  null ,Criteria::ISNULL));

    $c->add($criterion);

    return CourseSubjectStudentExaminationPeer::doCount($c);
  }

  public function countStudentRepprovedCourseSubject($approved = null)
  {
    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, $this->getId());
    $c->addJoin(CourseSubjectStudentPeer::ID,StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID);

    if (! is_null($approved))
    {
      if ($approved)
      {
        $c->add(StudentRepprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNOTNULL);
      }
      else
      {
        $c->add(StudentRepprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNULL);
      }

    }

    return StudentRepprovedCourseSubjectPeer::doCount($c);
  }

  /*
   * Retrieves number of examinations for examination instance (December, February) given as parameter.
   */
   public function countExaminationsForExaminationNumber($examination_number)
  {
    $es = ExaminationSubjectPeer::retrieveForCareerSubjectSchoolYearAndExaminationNumber($this->getCareerSubjectSchoolYear(), $examination_number);
    $total = 0;

    if ($es)
    {
      foreach (CourseSubjectStudentExaminationPeer::doSelectForExaminationSubjectAndNumber($es, $examination_number) as $csse)
      {
        // Could be improved a lot...
        if ($csse->getCourseSubjectStudent()->getCourseSubject()->getCourse()->getDivision() == $this->getCourse()->getDivision())
        {
          $total++;
        }
      }
    }
    return $total;
  }


  public function addStudentsFromCourseSubject($students, $origin_course_subject, $con = null)
  {
    if (!$this->getCourse()->canMoveStudents())
    {
      throw (new Exception());
    }

    if (is_null($con))
      $con = Propel::getConnection();

    $con->beginTransaction();
    try
    {
      foreach ($students as $student_id)
      {
        $css_origin = CourseSubjectStudentPeer::retrieveByCourseSubjectAndStudent($origin_course_subject->getId(), $student_id);
        $css_origin->setCourseSubjectId($this->getId());
        $css_origin->save($con);

        //para las asistencias
        $c = new Criteria();
	      $c->add(StudentAttendancePeer::STUDENT_ID, $student_id);
        foreach ($origin_course_subject->getStudentAttendances($c) as $sa)
        {
          $sa->setCourseSubject($this);
          $sa->save($con);
        }
      }
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
      throw $e;
    }
  }

  public function getCourseSubjectAndTeacherToString()
  {
    return sprintf('%s, %s (%s)', $this->getCourse(), $this->getCareerSubjectToString(), $this->getCourse()->getTeachersStr());
  }

	public function pathwayClose(PropelPDO $con)
	{
		foreach ($this->getCourseSubjectStudentPathways() as $course_subject_student_pathway)
		{
			$evaluator_instance = SchoolBehaviourFactory::getEvaluatorInstance();

				if ($course_subject_student_pathway->getMark() >= $evaluator_instance->getPathwayPromotionNote())
				{

					$original_course_subject_student = $course_subject_student_pathway->getRelatedCourseSubjectStudent();

					$course_marks_avg =  SchoolBehaviourFactory::getEvaluatorInstance()->getMarksAverage($original_course_subject_student, $con);
					$final_mark = bcdiv($course_subject_student_pathway->getMark() + $course_marks_avg, 2, 2);

					$sacs = new StudentApprovedCareerSubject();
					$sacs->setCareerSubject($this->getCareerSubjectSchoolYear()->getCareerSubject());
					$sacs->setMark($final_mark);
					$sacs->setStudent($course_subject_student_pathway->getStudent());
					$sacs->setSchoolYear($this->getCourse()->getSchoolYear());
					$original_course_subject_student->getCourseResult()->setStudentApprovedCareerSubject($sacs);
					$original_course_subject_student->save($con);

					$srcs = StudentRepprovedCourseSubjectPeer::retrieveByCourseSubjectStudent($original_course_subject_student);
					if (is_null($srcs)) {
						$srcs = new StudentRepprovedCourseSubject();
						$srcs->setCourseSubjectStudent($original_course_subject_student);
					}
					$srcs->setStudentApprovedCareerSubject($sacs);
					$srcs->save($con);
					$sers = StudentExaminationRepprovedSubjectPeer::retrieveByStudentRepprovedCourseSubject($srcs);
					// TODO: pongo en blanco la referencia a una mesa de previa??

					if (is_null($sers)) {
						$sers = new StudentExaminationRepprovedSubject();
						$sers->setStudentRepprovedCourseSubject($srcs);
					}
					$sers->setMark($course_subject_student_pathway->getMark());
					$sers->setExaminationRepprovedSubject(null);
					$sers->save($con);
				}
		}
	}
        
    public function getCourseSubjectStudentsForPrintCalifications($criteria = null, PropelPDO $con = null)
    {
        if ($criteria === null)
        {
          $criteria = new Criteria();
        }
        
        //busco los retirados o con reserva de banco
        $c = new Criteria();
        $c->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
        $criterion = $c->getNewCriterion(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN, Criteria::EQUAL);
        $criterion->addOr($c->getNewCriterion(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE, Criteria::EQUAL));
        $c->add($criterion);
        $c->clearSelectColumns();
        $c->addSelectColumn(StudentPeer::ID);
        $stmt = StudentPeer::doSelectStmt($c);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN); 

        $criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID);
        $criteria->add(CourseSubjectStudentPeer::IS_NOT_AVERAGEABLE, false);
        $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
        $criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentCareerSchoolYearPeer::STUDENT_ID);
        $criteria->add(StudentPeer::ID, $ids, Criteria::NOT_IN);
        $criteria->add(PersonPeer::IS_ACTIVE,TRUE);
        $criteria->setDistinct();    
        $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);

        return parent::getCourseSubjectStudents($criteria);
    }
    
    public function getCourseSubjectStudentsForPrintReport($criteria = null, PropelPDO $con = null)
  {
    if ($criteria === null)
    {
      $criteria = new Criteria();
    }

     //busco los retirados o con reserva de banco
     $c = new Criteria();
     $c->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
     $c->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE, Criteria::EQUAL);
     $c->clearSelectColumns();
     $c->addSelectColumn(StudentPeer::ID);
     $stmt = StudentPeer::doSelectStmt($c);
     $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
      
     $criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID);
     $criteria->add(CourseSubjectStudentPeer::IS_NOT_AVERAGEABLE, false);
     $criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
     $criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentCareerSchoolYearPeer::STUDENT_ID);
     $criteria->add(StudentPeer::ID, $ids, Criteria::NOT_IN);
     $criteria->setDistinct();    
     $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);

     return parent::getCourseSubjectStudents($criteria);
  }
}
