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
 * @author gramirez
 */
abstract class InterfaceSchoolBehaviour
{

  /**
   * Returns the form factory for current behaviour
   *
   * @return SchoolBehaviourFormFactory   returns associated form factory
   */
  abstract public function getFormFactory();

  /**
   * Returns default Country selection
   *
   * @return int State id
   */
  abstract public function getDefaultCountryId();

  /**
   * Returns default State selection
   *
   * @return int State id
   */
  abstract public function getDefaultStateId();

  /**
   * Returns default City selection
   *
   * @return int City Id
   */
  abstract public function getDefaultCityId();

  /**
   * Determines the way the school manage file numbers for students: if students have
   * only one file number for every career or it will have a file number per registered
   * career. Default behaviour is a file number per career
   *
   * @return Boolean x
   */
  abstract public function getFileNumberIsGlobal();

  abstract public function setStudentFileNumberForCareer(CareerStudent $career_student, PropelPDO $con=null);


  /**
 * Returns a Criteria object with conditions applied so it can be used to retrieve
 * every CareerSubjects objects (for example with CareerSubjectPeer::doSelect) that
 * can potentially select receiving CareerSubject object $cs as their correlatives.
 * $exclude_related dictates if consider current correlatives or not
 *
 * @param CareerSubject $cs object to analyze which CareerSubjects are subject of select us as correlative
 * @param bool $exclude_related
 * @param Criteria $criteria pre initialized criteria
 * @param PropelPDO $con
 * @return Criteria
 */
  abstract public function getAvailableCarrerSubjectsAsCorrelativesCriteriaFor(CareerSubject $cs, $exclude_related=true, Criteria $criteria = null, PropelPDO $con=null);



  /**
   * Get every student that isnt inscripted in other division.
   * The inscription depends on the aproval method implemented by each school
   *
   * @param  Division     $division
   *
   * @return array Student[]
   */
  abstract public function getAvailableStudentsForDivision(Division $division);

  /**
   * Get every student that can be add to a CourseSubject
   * The inscription depends on the aproval method implemented by each school
   *
   * @param  CourseSubject     $course_subject
   *
   * @return array Student[]
   */
  abstract public function getAvailableStudentsForCourseSubject(CourseSubject $course_subject, $criteria = null);

  /**
   * This method returns the available students for a course subject. If $filter_by_orientation == true then filter by orientation too.
   *
   * @param CourseSubject $course_subject
   * @param Criteria $criteria
   * @param Boolean $filter_by_orientation
   * @return Criteria
   */
  abstract public function getAvailableStudentsForCourseSubjectCriteria(CourseSubject $course_subject,  $criteria = null,  $filter_by_orientation = false);

  /**
   * This methods returns the students available four a course that have a Division
   *
   * @see getAvailableStudentsForCourseSubjectCriteria
   *
   * @param CourseSubject $course_subject
   * @param Criteria $criteria
   * @param Boolean $filter_by_orientation
   * @return Criteria
   */
  abstract public function getAvailableStudentsForDivisionCourseSubject(CourseSubject $course_subject, $criteria = null, $filter_by_orientation = true);

  /**
   * This methods returns the students available four a course that have a Division
   *
   * @see getAvailableStudentsForCourseSubjectCriteria
   *
   * @param CourseSubject $course_subject
   * @param Criteria $criteria
   * @param Boolean $filter_by_orientation
   * @return Criteria
   */
  abstract public function getAvailableStudentsForDivisionCourseSubjectCriteria(CourseSubject $course_subject, $criteria = null, $filter_by_orientation = false);

  /**
   * Get every CareerSubjectSchoolYear that has been marked as 'is_option' that is
   * available for $career_subject_school_year (the optional CareerSubject).
   *
   * @param  CareerSubjectSchoolYear $career_subject The optional CareerSubject.
   * @param  Boolean       $exclude_related, if its true excludes the related options
   * @param  PropelPDO     $con            Database connection (can be null).
   *
   * @return Criteria
   */
  abstract public function getAvailableChoicesForCareerSubjectSchoolYearCriteria(CareerSubjectSchoolYear $career_subject_school_year, $exclude_related = true, $exclude_repetead = true,PropelPDO $con = null);

  /**
   * Get every CareerSubjectSchoolYear that has been marked as 'is_option' that is
   * available for $career_subject_school_year (the optional CareerSubject).
   *
   * @param  CareerSubjectSchoolYear $career_subject The optional CareerSubject.
   * @param  Boolean       $exclude_related, if its true excludes the related options
   * @param  PropelPDO     $con            Database connection (can be null).
   *
   * @return array OptionalCareerSubject[]
   */
  abstract public function getAvailableChoicesForCareerSubjectSchoolYear(CareerSubjectSchoolYear $career_subject_school_year, $exclude_related = true, PropelPDO $con = null);

/**
 * Returns those CareerSubjects that are correlative for $career_subject. This depends on
 * has_correlative_previous_year flag
 *
 * @param CareerSubject $career_subject
 * @param Criteria $criteria add custom filters to returned values
 * @param PropelPDO $con
 * @return array CareerSubject[]
 */
  abstract public function getCorrelativesForCareerSubject(CareerSubject $career_subject, Criteria $criteria = null, PropelPDO $con = null);


  /**
   * Returns an array of CareerSubjects that has $career_subject as their correlative
   *
   * @param CareerSubject $career_subject
   * @param Criteria $criteria
   * @param PropelPDO $con
   * @return array CareerSubject[]
   */
  abstract public function getCareerSubjectCorrelativesOf(CareerSubject $career_subject, Criteria $criteria=null, PropelPDO $con=null);




/**
 * Returns the number that represents the first year for any career in this school
 * @return int the first year (Default 1)
 */
  abstract public function getMinimumCareerYear();

  /**
 *******************************************************************************
 *******************************************************************************
 *
 * The following methods are for Student assignment to related entities
 *
 *******************************************************************************
 *******************************************************************************
 */
/**
 * Return available career for a student to be registered. Some schools may want to exclude students
 * with same conditions.
 * Returns every career
 *
 * @param Student $student
 * @param Criteria $criteria
 * @param <type> $exclude_related
 * @param PropelPDO $con
 * @return <type>
 */
  abstract public function getAvailableCareerForStudentCriteria(Student $student, $exclude_related = true, Criteria $criteria = null, PropelPDO $con=null);

  /**
   * Returns the first weekday that a course can be coursed.
   * 1 (monday) through 7 (sunday)
   *
   * @return integer
   */
  abstract public function getFirstCourseSubjectWeekday();

  /**
   * Returns the last weekday that a course can be coursed.
   * 1 (monday) through 7 (sunday)
   *
   * @return integer
   */
  abstract public function getLastCourseSubjectWeekday();

  /**
   * Returns the available students for the given examination repproved subject.
   *
   * @param ExaminationRepprovedSubject $examination_repproved_subject
   * @return array
   */
  public abstract function getAvailableStudentsForExaminationRepprovedSubject(ExaminationRepprovedSubject $examination_repproved_subject , $is_new=null);

  /**
   * This method returns a dir for a yml file that draws the menu.
   *
   * @return string
   */
  abstract public function getMenuYaml();

  /**
   * This method returns an array of object actios for schoolyear module
   *
   * return array;
   */
  abstract public function getListObjectActionsForSchoolYear();

  /**
   * this method creates the alloweds for this career_student
   *
   * @see createStudentCareerSubjectAlloweds in CareerStudent.
   *
   * @param CareerStudent $career_student
   * @param Integet $start_year
   * @param PropelPDO $con
   *
   */
  abstract public function createStudentCareerSubjectAlloweds(CareerStudent $career_student, $start_year, PropelPDO $con);

  /**
   * This method returns an array of attendance types (Day or subject)
   *
   * @return array
   */
  abstract public function getAttendanceTypeChoices();

    /**
   *this method returns true when the attendance type is subject_attendance
   *
   * @return boolean
   */
  abstract public function hasSubjectAttendance();


  /**
   * This method returns a Criteria with the career_subjects that can be passed by equivalence of the student.
   *
   * @param integer $career_id
   * @param Student $student
   *
   * @return Criteria $c
   */
  abstract public function getCareerSubjectsForEquivalenceCriteria ($career_id , Student $student);

  /**
   * This method check if the student has approved all the correlatives for the careerSubject.
   *
   * @param CareerSubject $career_subject
   * @param Student $student
   * @return boolean
   */
  abstract public function canAddEquivalenceFor(CareerSubject $career_subject, Student $student);

  /**
   * Return the title for the exportation in shared_course_subject
   *
   * @param Teacher $teacher
   * @return string
   */
  abstract public function getExportationSharedCourseSubjectTitle(Teacher $teacher);

  /**
   * This method returns the absences depending of arguments:
   *
   * IF period_id is null, then returns all the absences.
   * IF course_subject_id is null then returns the absences per day.
   * IF include_justificated is null, then excludes the absences justificated.
   *
   * @param type $career_school_year_id
   * @param type $student_id
   * @param type $period_id
   * @param type $course_subject_id
   * @param type $include_justificated
   *
   * @return StudentAttendance array
   */
  abstract public function getAbsences($career_school_year_id, $student_id, $period_id = null,$course_subject_id = null);

  /**
   * This method check if exists a student_free with the parameters.
   * IF NOT exists then the student inst free, otherwise check the value of the student_free
   *
   * @see StudentPeer::retrieveByStudentCarreerSchoolyearPeriodAndCourse
   *
   * @param Student $student
   * @param CareerSchoolYearPeriod $career_school_year_period
   * @param type $course_subject
   *
   * @return boolean
   */
  abstract public function isFreeStudent(Student $student, CareerSchoolYearPeriod $career_school_year_period= null, CourseSubject $course_subject = null, CareerSchoolYear $career_school_year);

  /**
   * This method return true if repproved subject ought to be shown in report cards Otherwise, returns false.
   *
   * @return boolean
   */
  abstract public function showReportCardRepproveds();
}