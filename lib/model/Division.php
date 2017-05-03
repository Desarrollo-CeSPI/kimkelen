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

class Division extends BaseDivision
{

  public function __toString()
  {
    return sprintf('%d %s', $this->getYear(), $this->getDivisionTitle()->getName());
  }

  public function getName()
  {
    return strval($this->getYear()) . ' ' . $this->getDivisionTitle()->getName();
  }

  public function canBeDeleted()
  {
    return $this->isCurrentSchoolYear() && !$this->hasStudents();
  }

  public function getMessageCantBeDeleted()
  {
    return "The division is't in the current school year";
  }

  public function getSchoolYear()
  {
    return $this->getCareerSchoolYear()->getSchoolYear();
  }

  public function getCareer()
  {
    return $this->getCareerSchoolYear()->getCareer();
  }

  public function getStudents(Criteria $c = null)
  {
    $ret = array();

    $c =($c == null) ? new Criteria: $c ;
    $c->add(StudentPeer::ID, SchoolYearStudentPeer::retrieveStudentIdsForSchoolYear($this->getSchoolYear()), Criteria::IN);
    $c->addJoin(DivisionStudentPeer::STUDENT_ID,  StudentPeer::ID);
    $c->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID, Criteria::INNER_JOIN);
    $c->add(PersonPeer::IS_ACTIVE, true);

    $c->addAscendingOrderByColumn(PersonPeer::LASTNAME);
    $c->addAscendingOrderByColumn(PersonPeer::FIRSTNAME);

    foreach ($this->getDivisionStudents($c) as $ds)
    {
      $ret[] = $ds->getStudent();
    }
    return $ret;
  }

  public function deleteStudentFromCourses($id, $con)
  {
    $c = new Criteria();
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID,CourseSubjectPeer::ID,Criteria::INNER_JOIN);
    $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID, Criteria::INNER_JOIN);
    $c->addAnd(CoursePeer::DIVISION_ID, $this->getId());
    $c->addAnd(CourseSubjectStudentPeer::STUDENT_ID, $id);

    foreach (CourseSubjectStudentPeer::doSelect($c) as $css)
    {
      if ($css->countValidCourseSubjectStudentMarks() > 0)
        throw new Exception('Los alumnos seleccionados poseen datos que le impiden ser borrados de esta division y sus cursos.');
      $css->delete($con);
    }
  }

  public function deleteStudents($con = null, $values = array())
  {
    if (is_null($con))
      $con = Propel::getConnection();

    if (empty($values))
    {
      return null;
    }

    $c = new Criteria();
    $c->add(StudentPeer::ID, SchoolYearStudentPeer::retrieveStudentIdsForSchoolYear($this->getSchoolYear()), Criteria::IN);
    $c->addJoin(DivisionStudentPeer::STUDENT_ID,  StudentPeer::ID);

    $con->beginTransaction();
    try
    {
      foreach ($this->getDivisionStudents($c) as $division_student)
      {
        if (!in_array($division_student->getStudentId(), $values))
        {
          $this->deleteStudentFromCourses($division_student->getStudentId(), $con);
        }
        $division_student->delete($con);
      }
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

  public function getCareerSubjects()
  {
    $c = new Criteria();
    $c->add(CoursePeer::DIVISION_ID, $this->getId());
    $c->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    CareerSubjectSchoolYearPeer::sorted($c);

    return CareerSubjectPeer::doSelect($c);
  }

  /**
   * This method returns an array of the CareerSubjects that arent associated with a course of this division.
   * In the specific case of an optative careersubject, check if the choices arent been already added.
   *
   * @return <array>  CareerSubject[]
   */
  public function getUnrelatedCareerSubjects()
  {
    $career_subject_ids = array_map(create_function('$o', 'return $o->getId();'), $this->getCareerSubjects());

    $c = new Criteria();
    $c->add(CareerSubjectPeer::CAREER_ID, $this->getCareerSchoolYear()->getCareerId());
    $c->add(CareerSubjectPeer::YEAR, $this->getYear());
    $c->add(CareerSubjectPeer::IS_OPTION, false);

    $c->add(CareerSubjectPeer::ID, $career_subject_ids, Criteria::NOT_IN);

    // Recorremos las careerSubject que no estan agregadas ya a un curso de la division
    $unrelated = array();
    foreach (CareerSubjectPeer::doSelect($c) as $cs)
    {
      //Si tiene opciones, entonces chequamos que no esten agregadas las opciones.
      if ($cs->getHasChoices())
      {
        $csy = CareerSubjectSchoolYearPeer::retrieveByCareerSubjectAndSchoolYear($cs, $this->getSchoolYear());

        $career_subject_school_year_ids = array_map(create_function('$o', 'return $o->getChoiceCareerSubjectSchoolYearId();'), $csy->getChoices());

        $c = new Criteria();
        $c->add(CoursePeer::DIVISION_ID, $this->getId());
        $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
        $c->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $career_subject_school_year_ids, Criteria::IN);
        if (CourseSubjectPeer::doCount($c) == 0)
        {
          $unrelated[] = $cs;
        }
      }
      else
      {
        $unrelated[] = $cs;
      }
    }

    return $unrelated;

  }

  public function createCourse($career_subject_school_year_id, PropelPDO $con = null)
  {
    $career_subject_school_year = CareerSubjectSchoolYearPeer::retrieveByPk($career_subject_school_year_id);
    $course = new Course();
    $course->setName($this->getName() . ' ' . $career_subject_school_year->getCareerSubject()->getSubjectName());
    $course->setDivision($this);
    $course->setSchoolYear($career_subject_school_year->getSchoolYear());
    $course->save($con);
    $career_subject_school_year->createCourseSubject($course, $con);

  }

  public function copyStudentsToCourses(PropelPDO $con = null)
  {
    if (is_null($con))
    {
      $con = Propel::getConnection(DivisionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    try
    {
      $con->beginTransaction();

      $courses = $this->getCourses();
      foreach ($courses as $course)
      {
        $course->copyStudentsFromDivision($con);
        $course->clearAllReferences(true);
        unset($course);
      }
      unset($courses);

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

  public function canBeEditedByPreceptorUser(sfGuardUser $preceptor_user)
  {
    $criteria = new Criteria();
    $criteria->addJoin(DivisionPeer::ID, DivisionPreceptorPeer::DIVISION_ID);
    $criteria->addJoin(DivisionPreceptorPeer::PRECEPTOR_ID, PersonalPeer::ID);
    $criteria->addJoin(PersonalPeer::PERSON_ID, PersonPeer::ID);
    $criteria->add(PersonPeer::USER_ID, $preceptor_user->getId());
    return DivisionPeer::doCount($criteria) > 0 && $this->isCurrentSchoolYear();

  }

  public function canBeSeenByPreceptorUser($preceptor_user)
  {
    return $this->canBeEditedByPreceptorUser($preceptor_user);

  }

  public function canBeSeenByTeacherUser(sfGuardUser $teacher_user)
  {
    $criteria = new Criteria();
    $criteria->add(DivisionPeer::ID, $this->getId());
    $criteria->addJoin(DivisionPeer::ID, CoursePeer::DIVISION_ID);
    $criteria->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
    $criteria->addJoin(CourseSubjectPeer::ID, CourseSubjectTeacherPeer::COURSE_SUBJECT_ID);
    $criteria->addJoin(CourseSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
    $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
    $criteria->add(PersonPeer::USER_ID, $teacher_user->getId());
    return DivisionPeer::doCount($criteria) > 0;

  }

  public function canCopyStudentsToCourses()
  {
    $can_copy = false;
    foreach ($this->getCourses() as $course)
    {
      $can_copy = $can_copy || $course->canCopyStudentsFromDivision();
    }
    return $can_copy;

  }

  public function canManageAttendance()
  {
    return $this->countStudents() && $this->canManageAttendanceForDay() && $this->isCurrentSchoolYear();

  }

  public function canPrintReportCards()
  {
    return $this->hasStudents() && $this->getCareerSchoolyearPeriods();

  }

  public function canLoadAttendances()
  {
    return $this->hasStudents() && $this->hasAttendanceForDay() && $this->isCurrentSchoolYear();

  }

  public function hasStudents()
  {
    return $this->countStudents() != 0;

  }

  public function canManageAttendanceForDay()
  {
    if (!$this->isCurrentSchoolYear())
    {
      return false;
    }

    if (count($this->getCourses()) != 0)
    {
      foreach ($this->getCourses() as $course)
      {
        if ($course->canManageAttendanceForDay())
        {
          return true;
        }
      }
    }
    else
    {
      return $this->getCareerSchoolYear()->isAttendanceForDay();
    }
  }

  public function getMessageCantManageAttendance()
  {
    if (!$this->isCurrentSchoolYear())
    {
      return "The division is't in the current school year";
    }


    if (count($this->getStudents()) == 0)
      return 'The division dont have students inscripted.';
    else
      return 'Not all the courses of the division, has the configuration of day assistance';

  }

  public function getMessageCantPrintReportCards()
  {
    if (count($this->getStudents()) == 0)
      return 'The division dont have students inscripted.';
    else
      return 'There are no periods configured for this career school year';

  }

  public function getMessageCantLoadAttendances()
  {
    if (!$this->isCurrentSchoolYear())
    {
      return "The division is't in the current school year";
    }
    if (count($this->getStudents()) == 0)
      return 'The division dont have students inscripted.';
    else
      return 'Not all the courses of the division, has the configuration of day assistance Or is not have any configuration';

  }

  public function countStudents()
  {
    return count($this->getStudents());
  }

  public function getPreceptorsString()
  {
    $division_preceptors = $this->getDivisionPreceptors();
    $first = array_shift($division_preceptors);

    if ($first)
    {
      $preceptors_str = $first->getPersonal()->__toString();
    }
    else
    {
      $preceptors_str = 'No tiene preceptores asignados';
    }

    foreach ($division_preceptors as $division_preceptor)
    {

      $preceptors_str .= '; ' . $division_preceptor->getPersonal()->__toString();
    }

    return $preceptors_str;

  }

  /**
   * Returns an arrays of WeekDays indicating each course that belongs to
   * this division
   *
   * @return array
   */
  public function getWeekCalendar(SchoolYear $school_year = null)
  {
    $days = array();
    foreach ($this->getCourses(true) as $course)
    {
      foreach ($course->getCourseSubjects() as $course_subject)
      {
        foreach ($course_subject->getCourseSubjectDays() as $course_day)
        {
          $days[] = $course_day->getWeekDay();
        }
      }
    }
    return $days;

  }

  public function canListStudents(PropelPDO $con = null)
  {
    return $this->countStudents();

  }

  public function getStudentsIds(Criteria $c = null)
  {
    return array_map(create_function('$cs', 'return $cs->getId();'), $this->getStudents());

  }

  public function getCareerSchoolYearPeriodIds()
  {
    $c = new Criteria();
    $c->add(CareerSchoolYearPeriodPeer::CAREER_SCHOOL_YEAR_ID, $this->getCareerSchoolYearId());
    $c->addAscendingOrderByColumn(CareerSchoolYearPeriodPeer::COURSE_TYPE);
    $c->addAscendingOrderByColumn(CareerSchoolYearPeriodPeer::START_AT);

    $array = array();
    $i = 0;
    foreach (CareerSchoolYearPeriodPeer::doSelect($c) as $csyp)
    {
      $array[$i] = $csyp->getId();
      $i++;
    }
    return $array;

  }

  public function getCareerSchoolyearPeriods()
  {
    $c = new Criteria();
    $c->add(CareerSchoolYearPeriodPeer::CAREER_SCHOOL_YEAR_ID, $this->getCareerSchoolYearId());
    $c->add(CareerSchoolYearPeriodPeer::COURSE_TYPE, $this->getCourseType());
    $c->addAscendingOrderByColumn(CareerSchoolYearPeriodPeer::COURSE_TYPE);
    $c->addAscendingOrderByColumn(CareerSchoolYearPeriodPeer::START_AT);

    return CareerSchoolYearPeriodPeer::doSelect($c);
  }

  public function getCourseType()
  {
    return $this->getCareerSchoolYear()->getCourseTypeForYear($this->getYear());
    //return SchoolBehaviourFactory::getInstance()->getDivisionCourseType();
  }


  public function canPrintCalification()
  {
    return true;

  }

  public function getConfigurationForm($course_type)
  {
    $form = new CourseSubjectConfigurationDivisionForm($this);
    $form->setCourseTypes($course_type);
    $form->configure();
    return $form;

  }

  public function getMaxAbsenceForPeriod($period = null)
  {
    if ($this->getIsAbsenceForPeriod())
    {
      $c = new Criteria();
      $c->add(CourseSubjectConfigurationPeer::DIVISION_ID, $this->getId());
      $c->add(CourseSubjectConfigurationPeer::CAREER_SCHOOL_YEAR_PERIOD_ID, $period->getId());

      $course_configuration = CourseSubjectConfigurationPeer::doSelectOne($c);

      $max_absence = is_null($course_configuration) ? sfConfig::get('app_max_absence', 10) : $course_configuration->getMaxAbsence();
    }
    else
    {
      $max_absence = $this->getCareerSchoolYear()->getMaxAbsenceInYear($this->getYear());
    }

    return $max_absence;

  }

  public function hasAttendanceForDay()
  {
    $result = false;

    if ( count($this->getCourses()) != 0 )
    {
      foreach ($this->getCourses() as $course)
      {
        $result = $result || $course->hasAttendanceForDay();
      }
    }
    else
    {
      $result = $this->getCareerSchoolYear()->isAttendanceForDay();
    }

    return $result;

  }

  public function getCourseSubjects()
  {
    $c = new Criteria();
    $c->add(CoursePeer::DIVISION_ID, $this->getId());
    $c->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    CareerSubjectSchoolYearPeer::sorted($c);

    return CourseSubjectPeer::doSelect($c);
  }

  public function getCourses($include_commissions = false)
  {
    $criteria = new Criteria();
    $criterion = $criteria->getNewCriterion(CoursePeer::DIVISION_ID, $this->getId());

    if ($include_commissions)
    {
      $criterion->addOr($criteria->getNewCriterion(CoursePeer::RELATED_DIVISION_ID, $this->getId()));
    }

    $criteria->add($criterion);

    return CoursePeer::doSelect($criteria);

  }

  public function countCourses(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
  {
    return $this->countCoursesRelatedByDivisionId($criteria, $distinct, $con);

  }

  public function hasCourseType($course_type)
  {
    foreach ($this->getCourseSubjects() as $course_subject)
    {
      if ($course_subject->getCourseType() == $course_type)
        return true;
    }

    return false;

  }

  public function copyPreceptorsToDivision(PropelPDO $con = null, $division)
  {
    $division_preceptors = $this->getDivisionPreceptors();

    foreach ($division_preceptors as $division_preceptor)
    {
      $new_division_preceptor = new DivisionPreceptor();
      $new_division_preceptor->setDivision($division);
      $new_division_preceptor->setPreceptorId($division_preceptor->getPreceptor()->getId());
      $new_division_preceptor->save($con);

      $new_division_preceptor->clearAllReferences(true);
      unset($new_division_preceptor);
      $division_preceptor->clearAllReferences(true);
      unset($division_preceptor);
    }
    unset($division_preceptors);

    $division->clearAllReferences(true);
    unset($division);

  }

  public function copyCoursesToDivision(PropelPDO $con = null, $copy_division, $career_school_year)
  {
    $courses = $this->getCourses();
    foreach ($courses as $course)
    {
      $course->createCopyForSchoolYear($con, $copy_division, $career_school_year);
      $course->clearAllReferences(true);
      unset($course);
    }
    unset($courses);

    $copy_division->clearAllReferences(true);
    unset($copy_division);

    $career_school_year->clearAllReferences(true);
    unset($career_school_year);

  }

  public function createCopyForSchoolYear(PropelPDO $con = null, $career_school_year)
  {
    try
    {
      $con->beginTransaction();

      $copy_division = new Division();
      $copy_division->setDivisionTitle($this->getDivisionTitle());
      $copy_division->setCareerSchoolYear($career_school_year);
      $copy_division->setShift($this->getShift());
      $copy_division->setYear($this->getYear());
      $copy_division->save($con);

      $this->copyPreceptorsToDivision($con, $copy_division);
      $this->copyCoursesToDivision($con, $copy_division, $career_school_year);

      if ($this->getYear() <= $career_school_year->getCareer()->getQuantityYears() && $this->getYear() > 1)
      {
        $copy_division->createStudentsForNextYear($con, $this->getCareerSchoolYear());
      }

      $copy_division->clearAllReferences(true);
      unset($copy_division);
      $career_school_year->clearAllReferences(true);
      unset($career_school_year);
      $this->clearAllReferences(true);

      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
    }
  }

  public function createStudentsForNextYear(PropelPDO $con = null, CareerSchoolYear $last_career_school_year)
  {

    //$old_division = DivisionPeer::retrieveByDivisionTitleAndYearAndSchoolYear($this->getDivisionTitle(), $this->getYear() - 1, $last_career_school_year);
    //$students = $old_division->getStudents();

    $c = new Criteria();
    $c->addJoin(StudentPeer::ID, DivisionStudentPeer::STUDENT_ID, Criteria::INNER_JOIN);
    $c->addJoin(DivisionStudentPeer::DIVISION_ID, DivisionPeer::ID, Criteria::INNER_JOIN);
    $c->addAnd(DivisionPeer::YEAR, $this->getYear() - 1);
    $c->addAnd(DivisionPeer::DIVISION_TITLE_ID, $this->getDivisionTitleId());
    $c->addAnd(DivisionPeer::CAREER_SCHOOL_YEAR_ID, $last_career_school_year->getId());
    $c->addAnd(StudentPeer::ID,SchoolYearStudentPeer::retrieveStudentIdsForSchoolYear($last_career_school_year->getSchoolYear()),Criteria::IN);
    $students = StudentPeer::doSelect($c);

    foreach ($students as $student)
    {
      $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $this->getCareerSchoolYear());
      StudentCareerSchoolYearPeer::clearInstancePool();
      //If the student has not repeated last year.
      if (!is_null($student_career_school_year) && !$student_career_school_year->getIsRepproved())
      {
        $division_student = new DivisionStudent();
        $division_student->setStudent($student);
        $division_student->setDivision($this);
        $division_student->save($con);

        $division_student->clearAllReferences(true);
        unset($division_student);
        $student_career_school_year->clearAllReferences(true);
        unset($student_career_school_year);
      }

      $student->clearAllReferences(true);
      unset($student);
    }

    StudentPeer::clearInstancePool();
    unset($students);
  }

  public function createAllCourses($con = null)
  {
    if (is_null($con))
    {
      $con = Propel::getConnection();
    }
    try
    {
      $con->beginTransaction();

/* @var $career_subject CareerSubject */
      foreach ($this->getUnrelatedCareerSubjects() as $career_subject)
      {
        $career_subject_school_year_id = CareerSubjectSchoolYearPeer::retrieveByCareerSubjectAndSchoolYear($career_subject,$this->getSchoolYear())->getId();
        $this->createCourse($career_subject_school_year_id, $con);
      }

      $con->commit();
    }
    catch (PropelException $e)
    {

    }

  }

  public function isCurrentSchoolYear()
  {
    return SchoolYearPeer::retrieveCurrent() == $this->getSchoolYear();

  }

  public function canManageConduct()
  {
    return $this->isCurrentSchoolYear();

  }

  public function canDivisionStudents()
  {
    return $this->isCurrentSchoolYear();

  }

  public function canDivisionPreceptors()
  {
    return $this->isCurrentSchoolYear();

  }
  public function hasAttendanceForSubject()
  {
    $result = false;
    foreach ($this->getCourses() as $course)
    {
      $result = $result || $course->hasAttendanceForSubject();
    }

    return $result;
  }

  public function getIsAbsenceForPeriod()
  {
    return $this->getCareerSchoolYear()->getIsAbsenceForPeriodInYear($this->getYear());
  }

   public function getCourseSubjectForCareerSubjectSchoolYear(CareerSubjectSchoolYear $cssy)
  {
    $c = new Criteria();
    $c->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID, Criteria::INNER_JOIN);
    $c->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $cssy->getId());
    $c->add(CoursePeer::DIVISION_ID, $this->getId());

    return CourseSubjectPeer::doSelectOne($c);
  }

  public function getDivisionStudent($student_id)
  {
    $c = new Criteria();
    $c->add(DivisionStudentPeer::STUDENT_ID, $student_id);
    $c->add(DivisionStudentPeer::DIVISION_ID, $this->getId());

    return DivisionStudentPeer::doSelectOne($c);
  }

  public function addStudentsFromDivision($students, $origin_division, $con = null)
  {
    if (!$this->canMoveStudents()){
      throw (new Exception());
    }

    if (is_null($con))
      $con = Propel::getConnection();

    $con->beginTransaction();
    try
    {
      foreach ($students as $student)
      {
        $division_student_origin = $origin_division->getDivisionStudent($student);
        $division_student_origin->setDivisionId($this->getId());
        $division_student_origin->save($con);

        foreach ($origin_division->getCourses() as $origin_course)
        {
          $course_subject = $origin_course->getCourseSubject();
          $cssy = $course_subject->getCareerSubjectSchoolYear();
          $css_origin = $course_subject->getCourseSubjectStudent($student);
          $cs_destiny = $this->getCourseSubjectForCareerSubjectSchoolYear($cssy);

	        $c = new Criteria();
	   
	        $c->add(StudentAttendancePeer::STUDENT_ID, $student);

          //para las asistencias
          foreach ($course_subject->getStudentAttendances($c) as $sa)
          {
            $sa->setCourseSubject($cs_destiny);
            $sa->save($con);
          }

          $css_destiny = $cs_destiny->getCourseSubjectStudent($student);

          if (!is_null($css_origin))
          {
            $css_origin->setCourseSubject($cs_destiny);
            $css_origin->save($con);
          }
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

  public function canMoveStudents()
  {
    foreach ($this->getCourses() as $course)
    {
      if ($course->getIsClosed())
        return false;
    }

    return true;
  }

  public function getStudentsWithAllSubjectsApproved()
  {
    $students = Array();
    foreach ($this->getStudents() as $student)
    {
      $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $this->getCareerSchoolYear());
      if (!is_null($student_career_school_year->getAnualAverage()))
      {
        $students[]= $student;
      }
    }
    return $students;
  }

  public function getStudentsWithDisapprovedSubjects()
  {
    $students = Array();
    foreach ($this->getStudents() as $student)
    {
      $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $this->getCareerSchoolYear());
      if (is_null($student_career_school_year->getAnualAverage()))
      {
        $students[]= $student;
      }
    }
    return $students;
  }
  
  public function canShowCourseResultReport()
  {
    foreach ($this->getCourses() as $course)
    {
      if (! $course->getIsClosed())
        return false;
    }
    
    return $this->hasStudents();
  }

}
sfPropelBehavior::add('Division', array('changelog'));