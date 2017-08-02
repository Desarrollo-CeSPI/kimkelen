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

class Course extends BaseCourse
{

  public function __toString()
  {
    return $this->getName();
  }

  public function isCurrentSchoolYear()
  {
    return SchoolYearPeer::retrieveCurrent() == $this->getSchoolYear();

  }

  /**
   * If all the course subjects has the same careerSchoolYear, then returns the CareerSchoolYear else returns null
   *
   * @return CareerSchoolYear
   */
  public function getCareerSchoolYear()
  {
    $course_subjects = $this->getCourseSubjects();
    $course_subject = array_shift($course_subjects);

    if (is_null($course_subject))
      return null;

    $career_school_year = $course_subject->getCareerSubjectSchoolYear()->getCareerSchoolyear();
    $all_equals = true;
    foreach ($course_subjects as $course_subject)
    {
      $all_equals = $all_equals && ($course_subject->getCareerSubjectSchoolYear()->getCareerSchoolYearId() == $career_school_year->getId());
    }

    return $all_equals ? $career_school_year : null;

  }

  /**
   * If all the course subjects has the same year, then returns the year else returns null
   *
   * @return int Year
   */
  public function getYear()
  {
    $course_subjects = $this->getCourseSubjects();
    $course_subject = array_shift($course_subjects);

    if (is_null($course_subject))
      return null;

    $year = $course_subject->getCareerSubjectSchoolYear()->getCareerSubject()->getYear();
    $all_equals = true;
    foreach ($course_subjects as $course_subject)
    {
      $all_equals = $all_equals && ($course_subject->getCareerSubjectSchoolYear()->getCareerSubject()->getYear() === $year);
    }

    return $all_equals ? $year : null;

  }

  /*
   * If the course has studens inscripted in any subject, can´t be edited
   *
   * @return bool
   */

  public function canBeEdited(PropelPDO $con = null)
  {
    return $this->countStudents() == 0;

  }

  /**
   * String representation of the cantBeEdited cause
   *
   * @return string
   */
  public function getMessageCantBeEdited()
  {
    $ret = ($this->countStudents() > 0 ) ? "El curso no puede editarse porque tiene alumnos inscriptos" : '';
    return $ret;

  }

  public function canListStudents(PropelPDO $con = null)
  {
    return $this->countStudents();

  }

  public function getMessageCantListStudents()
  {
    return "The course hasnt any student inscripted.";

  }

  /*
   * If the course hasn´t students qualified can be deleted
   *
   * @return bool
   */

  public function canBeDeleted(PropelPDO $con = null)
  {
    return ($this->isPathway())? $this->countPathwayStudents() == 0 : $this->countStudents() == 0;
  }

  /**
   * String representation of the cantBeClosed cause
   * @return string
   */
  public function getMessageCantBeClosed()
  {
    if ($this->countStudents() == 0)
    {
      return 'The course hasnt any student inscripted.';
    }
    else
    {
      return 'You must calificate all the students in the course.';
    }

  }

	/**
	 * String representation of the cantBeClosed cause
	 * @return string
	 */
	public function getMessageCantClosePathway()
	{
		if ($this->countPathwayStudents() == 0)
		{
			return 'The course hasnt any student inscripted.';
		}
		else
		{
			return 'You must calificate all the students in the course.';
		}

	}

  /**
   * String representation of the cantBeDeleted cause
   *
   * @return string
   */
  public function getMessageCantBeDeleted()
  {
    return 'El curso no puede eliminarse por que tiene alumnos inscriptos';

  }

  /*
   * This method returns how many students are inscripted in any of the subjects of this course
   *
   * @return int
   */

  public function countStudents()
  {
    $criteria = CoursePeer::retrieveStudentsCriteria($this->getId());
    return CourseSubjectStudentPeer::doCount($criteria);

  }

	public function countPathwayStudents()
	{
		$criteria = CoursePeer::retrievePathwayStudentsCriteria($this->getId());
		return CourseSubjectStudentPathwayPeer::doCount($criteria);

	}

  /*
   * This method returns how many students has been qualified. At any courseSubject
   *
   * @return int
   */

  public function countStudentMarks()
  {
    $c = new Criteria();
    $c->add(CourseSubjectPeer::COURSE_ID, $this->getId());
    $c->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID);
    $c->addJoin(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->add(CourseSubjectStudentMarkPeer::MARK, null, Criteria::ISNOTNULL);
    return CourseSubjectStudentMarkPeer::doCount($c);

  }

  /*
   * This method returns an Array of Students that are inscripted in the subjects of this course
   *
   * @return array Students[]
   */

  public function getStudents()
  {
    $criteria = CoursePeer::retrieveStudentsCriteria($this->getId());
    return StudentPeer::sorted($criteria);

  }

	/**
	 * Return if the course marks can be edited.
	 *
	 * @param PropelPDO $con
	 *
	 * return boolean
	 */
	public function canEditPathwayMarks(PropelPDO $con = null)
	{
		return ($this->countPathwayStudents()) && (!$this->getIsClosed());

	}

  /**
   * Return if the course marks can be edited.
   *
   * @param PropelPDO $con
   *
   * return boolean
   */
  public function canEditMarks(PropelPDO $con = null)
  {
    return ($this->countStudents()) && (!$this->getIsClosed()) && ($this->isCurrentSchoolYear());

  }

  public function getMessageCantEditMarks()
  {
    $msj = ($this->countStudents() == 0) ? "Marks cant be edited because the course dont have students inscripted." : "";
    $msj = ($this->getIsClosed()) ? "Marks cant be edited because the course is closed." : "";

    return $msj;

  }

  public function canManageSubjects(PropelPDO $con = null)
  {
    return ($this->countStudents() == 0) && $this->isCurrentSchoolYear();
    ;

  }

  public function getMessageCantManageSubjects()
  {
    $msj = ($this->countStudents()) ? "The subjects cant be edited because the course has students associated." : "";

    return $msj;

  }

  public function getCourseSubjectsForUser($sf_user)
  {
    $c = new Criteria();
    if ($sf_user->isTeacher())
    {
      $c->add(PersonPeer::USER_ID, $sf_user->getGuardUser()->getId());
      $c->addJoin(PersonPeer::ID, TeacherPeer::PERSON_ID, Criteria::INNER_JOIN);
      $c->addJoin(TeacherPeer::ID, CourseSubjectTeacherPeer::TEACHER_ID, Criteria::INNER_JOIN);
      $c->addJoin(CourseSubjectTeacherPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID, Criteria::INNER_JOIN);
      $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID, Criteria::INNER_JOIN);
    }

    return $this->getCourseSubjectsOrdered($c);

  }

  public function getCourseSubjectsOrdered(Criteria $c = null)
  {
    $c = is_null($c) ? new Criteria() : $c;

    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    $c->addJoin(CareerSchoolYearPeer::CAREER_ID, CareerPeer::ID);
    $c->addAscendingOrderByColumn(CareerPeer::CAREER_NAME);
    $c->addJoin(CareerPeer::ID, CareerSubjectPeer::CAREER_ID);
    $c->addAscendingOrderByColumn(CareerSubjectPeer::YEAR);
    $c->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::ID, CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID);
    $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    $c->add(CoursePeer::ID, $this->getId());

    return $this->getCourseSubjects($c);

  }

  public function canBeClosed(PropelPDO $con = null)
  {
    if ($this->getIsClosed())
      return false;

    if ($this->countStudents() == 0)
      return false;
      
      
     //busco los retirados o con reserva de banco
    $criteria = new Criteria();
    $criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
    $criterion = $criteria->getNewCriterion(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN, Criteria::EQUAL);
    $criterion->addOr($criteria->getNewCriterion(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE, Criteria::EQUAL));
    $criteria->add($criterion);
	$criteria->clearSelectColumns();
    $criteria->addSelectColumn(StudentPeer::ID);
    $stmt = StudentPeer::doSelectStmt($criteria);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $c = new Criteria();
    $c->addJoin(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID);
    $c->add(CourseSubjectStudentPeer::IS_NOT_AVERAGEABLE, false);
    $c->addJoin(StudentPeer::PERSON_ID,  PersonPeer::ID);
    $c->addJoin(PersonPeer::IS_ACTIVE,true);

    $c->addJoin(CourseSubjectPeer::COURSE_ID, $this->getId());

    $c->add(CourseSubjectStudentMarkPeer::MARK_NUMBER, $this->getCurrentPeriod());
    $c->add(CourseSubjectStudentMarkPeer::MARK, null, Criteria::ISNULL);
    
    $c->add(StudentPeer::ID, $ids, Criteria::NOT_IN);
    
    return CourseSubjectStudentMarkPeer::doCount($c) == 0 && $this->isCurrentSchoolYear();

  }

  /*
   * Only for courses that have a division.
   * If the course have only one subject, then this method adds to the course_subject all the division students.
   */

  public function copyStudentsFromDivision(PropelPDO $con = null)
  {
    $course_subjects = $this->getCourseSubjects();

    foreach ($course_subjects as $course_subject)
    {
      $course_subject->copyStudentsFromDivision($con);
      $course_subject->clearAllReferences(true);
      unset($course_subject);
    }

    unset($course_subjects);

  }

  public function canCopyStudentsFromDivision()
  {
    if ($this->getIsClosed())
      return false;

    $can_copy = false;
    foreach ($this->getCourseSubjects() as $course_subject)
    {
      $can_copy = $can_copy || $course_subject->canCopyStudentsFromDivision();
    }
    return $can_copy;

  }

  public function getMessageCantCopyStudentsFromDivision()
  {
    if ($this->getIsClosed())
      return 'Cant copy from division, because the course is closed.';

    return 'Cant copy from division, because all the students are already inscripted.';

  }

  /*
   * This method returns all the course subjects that not are option.
   */

  public function getNonOptionCourseSubjects($criteria = null)
  {
    $criteria = $criteria ? $criteria : new Criteria();
    $criteria->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $criteria->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
    $criteria->add(CareerSubjectPeer::IS_OPTION, false);

    return $this->getCourseSubjects($criteria);

  }

  /*
   * This method returns all the course subjects that dont have options.
   */

  public function getNonOptionalCourseSubjects($criteria = null)
  {
    $criteria = $criteria ? $criteria : new Criteria();
    $criteria->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $criteria->addJoin(CareerSubjectPeer::ID, CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID);
    $criteria->add(CareerSubjectPeer::HAS_OPTIONS, false);

    return $this->getCourseSubjects($criteria);

  }

  public function close()
  {
    $con = Propel::getConnection();
    try
    {
      $con->beginTransaction();
      $all_closed = true;
      $course_subjects = $this->getCourseSubjects();
      foreach ($course_subjects as $cs)
      {
        $result = $cs->close($con, $this->getCurrentPeriod());
        $all_closed = $all_closed && $result;
      }

      if ($all_closed)
      {
        $this->setIsClosed(true);
      }

      $this->updatePeriod($con);


      $this->save($con);
      $con->commit();
    }
    catch (Exception $e)
    {
      throw $e;
      $con->rollBack();
    }

  }

  /**
   * This method updates the actual period of the course.
   *
   * @param PropelPDO $con
   */
  public function updatePeriod(PropelPDO $con = null)
  {
    $c = new Criteria();
    $c->add(CourseSubjectStudentMarkPeer::IS_CLOSED, true);
    $c->add(CourseSubjectStudentMarkPeer::MARK, null,Criteria::ISNOTNULL);
    $c->addJoin(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->add(CourseSubjectStudentPeer::IS_NOT_AVERAGEABLE, false);
    $c->add(CourseSubjectPeer::COURSE_ID, $this->getId());
    $c->addDescendingOrderByColumn(CourseSubjectStudentMarkPeer::MARK_NUMBER);

    $course_subject_student_mark = CourseSubjectStudentMarkPeer::doSelectOne($c);
    $period = is_null($course_subject_student_mark) ? 1 : $course_subject_student_mark->getMarkNumber();
    $this->setCurrentPeriod($period + 1);

  }

  public function canBeStudentMarksEditedByTeacherUser($teacher_user)
  {
    return $this->canBeEditedByTeacherUser($teacher_user);

  }

  public function canBeStudentMarksEditedByPreceptorUser($preceptor_user)
  {
    if ($this->getDivision())
    {
      $criteria = new Criteria();
      $criteria->add(CoursePeer::ID, $this->getId());
      $criteria->addJoin(CoursePeer::DIVISION_ID, DivisionPeer::ID);
      $criteria->addJoin(DivisionPeer::ID, DivisionPreceptorPeer::DIVISION_ID);
      $criteria->addJoin(DivisionPreceptorPeer::PRECEPTOR_ID, PersonalPeer::ID);
      $criteria->addJoin(PersonalPeer::PERSON_ID, PersonPeer::ID);
      $criteria->add(PersonPeer::USER_ID, $preceptor_user->getId());
    }
    else
    {

      PersonalPeer::joinWithCourse($criteria, $preceptor_user->getId());
    }

    return CoursePeer::doCount($criteria);

  }

  public function canBeEditedByTeacherUser($teacher_user)
  {
    $criteria = new Criteria();
    $criteria->setDistinct(CoursePeer::ID);
    $criteria->add(CoursePeer::ID, $this->getId());
    $criteria->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
    $criteria->addJoin(CourseSubjectPeer::ID, CourseSubjectTeacherPeer::COURSE_SUBJECT_ID);
    $criteria->addJoin(CourseSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
    $criteria->addJoin(TeacherPeer::PERSON_ID, PersonPeer::ID);
    $criteria->add(PersonPeer::USER_ID, $teacher_user->getId());
    return CoursePeer::doCount($criteria);

  }

  public function canBeEditedByPreceptorUser($preceptor_user)
  {
    $criteria = new Criteria();
    PersonalPeer::joinWithCourse($criteria, $preceptor_user->getId());
    return CoursePeer::doCount($criteria);

  }

  public function canManageCourseDays()
  {
    return $this->countCourseSubjects() && $this->isCurrentSchoolYear();

  }

  public function getMessageCantManageCourseDays()
  {
    return "The course day can't be managed because the course does not have any course subjects.";

  }

  public function canCourseSubjectStudent()
  {
    return $this->countCourseSubjects() && $this->isCurrentSchoolYear();
  }

  public function canCopyStudentsFromOtherCourse()
  {
    return $this->countStudents() == 0 && $this->isCurrentSchoolYear();
  }

  public function getMessageCantCourseSubjectStudent()
  {
    return "Students can't be managed because the course does not have any subject.";

  }

  public function canSeeAttendanceSheet()
  {
    return $this->countStudents() && $this->countCourseSubjects() && $this->hasAttendanceForSubject();
  }

  public function getMessageCantSeeAttendanceSheet()
  {
    if (!$this->hasAttendanceForSubject())
    {
      return "The course has not been configured with attendance for subject.";
    }

    return "The course hasnt any student inscripted.";
  }

  public function canCourseTeachers()
  {
    return $this->canBeEdited() && $this->countCourseSubjects();

  }

  public function getMessageCantCourseTeachers()
  {
    return "Course teachers can't be managed beacuse the course can't be edited or because there are not subjects set.";

  }

  /**
   * This method returns the subjects related with this course.
   *
   * @return <array> Subject[]
   */
  public function getSubjects()
  {
    return SubjectPeer::retrieveForCourse($this);
  }

  /**
   * This method returns the name of the subjects related with this course.
   *
   * @return <array> string
   */
  public function getSubjectsStr()
  {
    return implode(SubjectPeer::retrieveForCourse($this), "; ");
  }

  public function countSubjects()
  {
    return SubjectPeer::countForCourse($this);

  }

  /**
   * This method returns an array of teachers related with the course_subjects of the course.
   *
   * @return <array>  Teacher[]
   */
  public function getTeachers()
  {
    $c = new Criteria();
    $c->add(CourseSubjectPeer::COURSE_ID, $this->getId());
    $c->addJoin(CourseSubjectPeer::ID, CourseSubjectTeacherPeer::COURSE_SUBJECT_ID, Criteria::INNER_JOIN);
    $c->addJoin(CourseSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
    $c->setDistinct();

    return TeacherPeer::doSelect($c);

  }

  public function getTeachersStr()
  {
    return implode($this->getTeachers(), "; ");
  }

  /**
   * This method returns an array of active teachers related with the course_subjects of the course.
   *
   * @return <array>  Teacher[]
   */
  public function getActiveTeachers()
  {
    $c = new Criteria();
    $c->add(CourseSubjectPeer::COURSE_ID, $this->getId());
    $c->addJoin(CourseSubjectPeer::ID, CourseSubjectTeacherPeer::COURSE_SUBJECT_ID, Criteria::INNER_JOIN);
    $c->addJoin(CourseSubjectTeacherPeer::TEACHER_ID, TeacherPeer::ID);
    $c->addJoin(PersonPeer::ID, TeacherPeer::PERSON_ID);
    $c->add(PersonPeer::IS_ACTIVE, true);
    $c->setDistinct();

    return TeacherPeer::doSelect($c);

  }

  /**
   * This method count the teachers of the course
   *
   * @method getTeachers
   *
   * @return integer
   */
  public function countTeachers()
  {
    return count($this->getTeachers());

  }

  /**
   * This method counts active teachers of the course
   *
   * @method getActiveTeachers
   *
   * @return integer
   */
  public function countActiveTeachers()
  {
    return count($this->getActiveTeachers());

  }

  /**
   * This method returns an array of CourseSubjectTeachers related with the course.
   *
   * @return <array>  CourseSubjectTeachers[]
   */
  public function getCourseSubjectTeachers()
  {
    $c = new Criteria();
    $c->add(CourseSubjectPeer::COURSE_ID, $this->getId());
    $c->addJoin(CourseSubjectPeer::ID, CourseSubjectTeacherPeer::COURSE_SUBJECT_ID);
    $c->setDistinct();

    return CourseSubjectTeacherPeer::doSelect($c);

  }

  /**
   * This method returns an array of preceptors related with the course.
   *
   * @return <array>  Preceptor[]
   */
  public function getPreceptors()
  {
    $c = new Criteria();
    $c->add(CoursePreceptorPeer::COURSE_ID, $this->getId());
    $c->addJoin(CoursePreceptorPeer::PRECEPTOR_ID, PersonalPeer::ID);
    $c->setDistinct();

    return PersonalPeer::doSelect($c);

  }

  public function getCourseSubjectIds(Criteria $c = null)
  {
    return array_map(create_function('$cs', 'return $cs->getId();'), $this->getCourseSubjects($c));

  }

  public function canCalificate(PropelPDO $con = null)
  {
    if ($this->getIsClosed())
      return false;

    return $this->countStudents();

  }

  public function getMessageCantCalificate()
  {
    if ($this->getIsClosed())
      return 'The chourse  cant be calificated, because is closed.';

    return 'The course cant be calificated, because dont have any students inscripted.';

  }

  public function getAttendanceType()
  {
    return SchoolBehaviourFactory::getInstance()->getAttendanceTypeFor($this);

  }

  /*
   * This methor returns an Array of CourseSubjectStudents that are related to the subjects of this course
   *
   * @return array CourseSubjectStudents[]
   */

  public function getCourseSubjectStudents()
  {
    $criteria = CoursePeer::retrieveCourseSubjectStudentsCriteria($this->getId());
    return CourseSubjectStudentPeer::doSelect($criteria);

  }

  public function canManageStudentsRegularity(PropelPDO $con = null)
  {
    return $this->countStudents() && $this->isCurrentSchoolYear();

  }

  public function canManageAttendanceForDay()
  {
    $result = true;
    foreach ($this->getCourseSubjects() as $course_subject)
    {
      $result = $result && $course_subject->getCareerSubjectSchoolYear()->isAttendanceForDay();
    }

    return $result;

  }

  public function canBackPeriod(PropelPDO $con = null)
  {
    if ($this->getCareerSchoolYear()->getIsProcessed())
      return false;

    return $this->getCurrentPeriod() != 1;

  }

  public function canBackPeriodCommission(PropelPDO $con = null)
  {
    if ($this->getCareerSchoolYear() && $this->getCareerSchoolYear()->getIsProcessed())
      return false;

    return $this->getCurrentPeriod() != 1 && $this->isCurrentSchoolYear();

  }

  /**
   * This method back the period to last one.
   */
  public function backPeriod()
  {
    if ($this->getCurrentPeriod() == 1)
    {
      return null;
    }
    $con = Propel::getConnection();
    $con->beginTransaction();
    try
    {
      $c = new Criteria();
      $c->add(CourseSubjectStudentMarkPeer::MARK_NUMBER, $this->getCurrentPeriod() - 1);
      $c->addJoin(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
      $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
      $c->add(CourseSubjectPeer::COURSE_ID, $this->getId());
      $course_subject_students_marks = CourseSubjectStudentMarkPeer::doSelect($c);
      foreach ($course_subject_students_marks as $course_subject_student_mark)
      {
        $course_subject_student_mark->setIsClosed(false);
        $course_subject_student_mark->save($con);
        //Seleccionamos todos los StudentDisapprovedCourseSubjec y StudentApprovedCourseSubject si es que tiene y los borramos
        $c = new Criteria();
        $c->add(StudentDisapprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, $course_subject_student_mark->getCourseSubjectStudentId());
        StudentDisapprovedCourseSubjectPeer::doDelete($c);
        //eliminando StudentApprovedCourseSubject
        $c = new Criteria();
        $course_subject_student = CourseSubjectStudentPeer::retrieveByPK($course_subject_student_mark->getCourseSubjectStudentId());
        $c->add(StudentApprovedCourseSubjectPeer::STUDENT_ID, $course_subject_student->getStudentId());
        $c->add(StudentApprovedCourseSubjectPeer::COURSE_SUBJECT_ID, $course_subject_student->getCourseSubjectId());
        StudentApprovedCourseSubjectPeer::doDelete($c);
      }
      $this->setCurrentPeriod($this->getCurrentPeriod() - 1);
      $this->setIsClosed(false);
      $this->save($con);
      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

  public function canManageStudents()
  {
    return !$this->getIsClosed() && $this->isCurrentSchoolYear();

  }

  public function getMessageCantManageStudents()
  {
    return 'Cant manage because, the course is closed.';

  }

  public function addPreceptor($sf_user)
  {
    $course_preceptor = new CoursePreceptor();
    $course_preceptor->setCourse($this);
    $course_preceptor->setPreceptorId($sf_user->getPreceptor()->getId());

    $course_preceptor->save();

  }

  /**
   * If subject has attendance for subject or is bimestral/quaterly of a term can be configurated, otherwise not.
   *
   */
  public function canConfigurate()
  {
    if (!$this->isCurrentSchoolYear())
    {
      return false;
    }

   $result = true;
    foreach ($this->getCourseSubjects() as $course_subject)
    {
      $is_bimester_or_quaterly_of_a_term =  ($course_subject->getCourseType() == CourseType::BIMESTER) || ($course_subject->getCourseType() == CourseType::QUATERLY_OF_A_TERM) ;

      if (!$is_bimester_or_quaterly_of_a_term && $course_subject->hasAttendanceForDay())
        {
        $result = false;
      }
    }

    return $result;
  }

  public function getCourseSubjectsWithAttendanceForSubject()
  {
    $course_subjects = array();

    foreach ($this->getCourseSubjects() as $course_subject)
    {
      if ($course_subject->hasAttendanceForSubject())
        $course_subjects[] = $course_subject;
    }

    return $course_subjects;

  }

  public function hasAttendanceForDay()
  {
    $result = false;

    foreach ($this->getCourseSubjects() as $course_subject)
    {
      $result = $result || $course_subject->getCareerSubjectSchoolYear()->isAttendanceForDay();
    }

    return $result;

  }

  public function getDivision()
  {
    return $this->getDivisionRelatedByDivisionId();

  }

  public function setDivision(Division $v = null)
  {
    $this->setDivisionRelatedByDivisionId($v);

  }

  public function canRelatedToDivision()
  {

    return SchoolBehaviourFactory::getInstance()->canRelatedToDivision($this->getDivision(),$this->isCurrentSchoolYear());
    #return is_null($division) && $is_current_school_year);

  }

  public function hasAttendanceForSubject()
  {
    return !$this->hasAttendanceForDay();

  }

  public function copyCourseSubjects(PropelPDO $con = null, Division $division = null, CareerSchoolYear $career_school_year, $copy_course)
  {
    $course_subjects = $this->getCourseSubjects();

    foreach ($course_subjects as $course_subject)
    {
      $copy_course_subject = new CourseSubject();
      $copy_course_subject->setCourse($copy_course);

      $career_subject_school_year = CareerSubjectSchoolYearPeer::retrieveByCareerSubjectAndSchoolYear($course_subject->getCareerSubjectSchoolYear()->getCareerSubject(), $career_school_year->getSchoolYear());

      CareerSubjectSchoolYearPeer::clearInstancePool();

      $copy_course_subject->setCareerSubjectSchoolYear($career_subject_school_year);
      $copy_course_subject->save($con);

      $course_subject->copyTeachersToCourseSubject($con, $copy_course_subject);
      $course_subject->copyCourseSubjectDays($con, $copy_course_subject);

      $career_subject_school_year->clearAllReferences(true);
      unset($career_subject_school_year);
      $copy_course_subject->clearAllReferences(true);
      unset($copy_course_subject);
      $course_subject->clearAllReferences(true);
      unset($course_subject);
    }
    unset($course_subjects);
    $this->clearAllReferences(true);

  }

  public function createCopyForSchoolYear(PropelPDO $con = null, Division $division = null, CareerSchoolYear $career_school_year)
  {
    $copy_course = new Course();
    $copy_course->setDivision($division);
    $copy_course->setName($this->getName());
    $copy_course->setQuota($this->getQuota());
    $copy_course->setSchoolYear($career_school_year->getSchoolYear());
    $copy_course->save($con);

    $this->copyCourseSubjects($con, $division, $career_school_year, $copy_course);

    $copy_course->clearAllReferences(true);
    unset($copy_course);
    $this->clearAllReferences(true);

  }

  public function canTeachers()
  {
    return $this->isCurrentSchoolYear();

  }

  public function canPreceptors()
  {
    return $this->isCurrentSchoolYear();

  }

  public function countCourseSubjectConfigurations()
  {
    $total = 0;
    foreach ($this->getCourseSubjects() as $course_subject)
    {
      $total+= $course_subject->countCourseSubjectCOnfigurations();
    }

    return $total;
  }

  public function getCourseSubject()
  {
    $c = new Criteria();
    $c->add(CourseSubjectPeer::COURSE_ID, $this->getId());

    return CourseSubjectPeer::doSelectOne($c);
  }

  public function canMoveStudents()
  {
    return !$this->getIsClosed();
  }
  
  public function isPathway()
  {
      return $this->getIsPathway();
  }
  
  public function getPathways()
  {
      $pathways = null;
      if ($this->isPathway())
      {
          if ($school_year = $this->getSchoolYear())
          {
              $pathways = $school_year->getPathways();
          }
      }
      
      return $pathways;
  }

  public function canManagePathwayCourseStudents()
  {
      return ( count($this->getCourseSubject()) > 0 );
  }

	public function CanClosePathway(PropelPDO $con = null)
	{
		if ($this->getIsClosed())
			return false;


		if ($this->countPathwayStudents() == 0)
			return false;

		$c = new Criteria();
		$c->addJoin(CourseSubjectStudentPathwayPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
		$c->addJoin(CourseSubjectStudentPathwayPeer::STUDENT_ID, StudentPeer::ID);
		$c->addJoin(StudentPeer::PERSON_ID,  PersonPeer::ID);
		$c->addJoin(PersonPeer::IS_ACTIVE,true);

		$c->addJoin(CourseSubjectPeer::COURSE_ID, $this->getId());

		$c->add(CourseSubjectStudentPathwayPeer::MARK, null, Criteria::ISNULL);

		return CourseSubjectStudentPathwayPeer::doCount($c) == 0;

	}

	public function pathwayClose()
	{
		$con = Propel::getConnection();
		try
		{
			$con->beginTransaction();
			$course_subjects = $this->getCourseSubjects();
			foreach ($course_subjects as $cs)
			{
				$cs->pathwayClose($con);
			}

			$this->setIsClosed(true);
			$this->save($con);
			$con->commit();
		}
		catch (Exception $e)
		{
			throw $e;
			$con->rollBack();
		}

	}
	
	public function getIsNotAverageableCourseSubjectStudent(){
		
		$c = new Criteria();
		$c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
		$c->addJoin(CourseSubjectPeer::COURSE_ID, $this->getId());
		$c->add(CourseSubjectStudentPeer::IS_NOT_AVERAGEABLE, true);

		return CourseSubjectStudentPeer::doSelect($c);
	}
	
	public function getIsAverageableCourseSubjectStudent(){
		
		$c = new Criteria();
		$c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
		$c->addJoin(CourseSubjectPeer::COURSE_ID, $this->getId());
		$c->add(CourseSubjectStudentPeer::IS_NOT_AVERAGEABLE, false);

		return CourseSubjectStudentPeer::doSelect($c);
	}
	
	public function canRevertCalificate(PropelPDO $con = null)
    {
		$course_subject= CourseSubjectPeer::retrieveByCourseId($this->getId());
		if(!is_null($course_subject)){
			$c = new Criteria();
		
			$c->add(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, $course_subject->getId());
			$c->add(CourseSubjectStudentPeer::IS_NOT_AVERAGEABLE, true);
			
			return (CourseSubjectStudentPeer::doCount($c) > 0);
		}		
		return true;				
    }
    
  public function canPathwayPreceptors()
  {    
    return TRUE; 
  }
  
  public function canPathwayAttendanceSubject()
  {
    //pregunto si la correlativa tiene falta por materia  
    if(! is_null($this->getCourseSubject()))
    {
        $correlatives = $this->getCourseSubject()->getCareerSubject()->getCareerSubjectsCorrelatives();
        $sy= SchoolYearPeer::retrieveCurrent();
        foreach($correlatives as $c)
        {
            $cssy = CareerSubjectSchoolYearPeer::retrieveByCareerSubjectAndSchoolYear($c, $sy);
            if($cssy->isAttendanceForDay())
            {
                return false;
            }
        }
    
        $sy=SchoolYearPeer::retrieveLastYearSchoolYear(SchoolYearPeer::retrieveCurrent());
        return (!$this->getIsClosed()  && $this->getSchoolYear()->getYear() == $sy->getYear());     
    }
    
    return false;
  }
}
sfPropelBehavior::add('Course', array('changelog'));
