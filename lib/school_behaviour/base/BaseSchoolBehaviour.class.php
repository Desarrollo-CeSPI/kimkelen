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

class BaseSchoolBehaviour extends InterfaceSchoolBehaviour
{

  protected $school_name = "Kimkelen";
  protected
  $_course_type_options = array(
    CourseType::TRIMESTER => 'Anual con Régimen Trimestral',
    CourseType::QUATERLY => 'Anual con Régimen Cuatrimestral',
    CourseType::BIMESTER => 'Cuatrimestral con Régimen Bimestral',
    CourseType::QUATERLY_OF_A_TERM => 'Cuatrimestral con Régimen de un termino'
  );

  const ATTENDANCE_DAY = 1;
  const ATTENDANCE_SUBJECT = 2;
  const DAYS_FOR_MULTIPLE_ATTENDANCE_FORM = 6;
  const BLOCKS_PER_COURSE_SUBJECT_DAY = 1;

  protected $HOUR_FOR_SCHEMA = array(7 => "07", 8 => "08", 9 => "09", 10 => 10, 11 => 11, 12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 18);
  protected $MINUTES_FOR_SCHEMA = array(0 => "00", 10 => 10, 20 => 20, 30 => 30, 40 => 40, 50 => 50);
  protected $attendance_types = array(
    self::ATTENDANCE_DAY => 'Por dia',
    self::ATTENDANCE_SUBJECT => 'Por materia'
  );
  protected $form_factory; /* Used for delegating custom forms used for several
   * actions depending on each school behaviour
   */

  public function __construct(BaseFormFactory $form_factory)
  {
    $this->form_factory = $form_factory;

  }

  /**
   * Returns the form factory for current behaviour
   *
   * @return SchoolBehaviourFormFactory   returns associated form factory
   */
  public function getFormFactory()
  {
    return $this->form_factory;

  }

  /**
   * Returns default Country selection
   *
   * @return int State id
   */
  public function getDefaultCountryId()
  {
    return Country::ARGENTINA;

  }

  /**
   * Returns default State selection
   *
   * @return int State id
   */
  public function getDefaultStateId()
  {
    return State::BUENOS_AIRES;

  }

  /**
   * Returns default City selection
   *
   * @return int City Id
   */
  public function getDefaultCityId()
  {
    return City::LA_PLATA;

  }

  /**
   * Determines the way the school manage file numbers for students: if students have
   * only one file number for every career or it will have a file number per registered
   * career. Default behaviour is a file number per career
   *
   * @return Boolean x
   */
  public function getFileNumberIsGlobal()
  {
    return false;

  }

  public function setStudentFileNumberForCareer(CareerStudent $career_student, PropelPDO $con = null)
  {
    $career_student->setFileNumber($career_student->getCareer($con)->getNextFileNumber($con));

  }

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
  public function getAvailableCarrerSubjectsAsCorrelativesCriteriaFor(CareerSubject $cs, $exclude_related = true, Criteria $criteria = null, PropelPDO $con = null)
  {
    $criteria = is_null($criteria) ? new Criteria() : $criteria;
    //Only consider career subjects of same career
    $criteria->addAnd(CareerSubjectPeer::CAREER_ID, $cs->getCareerId());
    $criteria->addAnd(CareerSubjectPeer::YEAR, $cs->getYear() - 1);  //Only consider previous year
    // Dont forget to consider orientation
    $orientation_criterion = $criteria->getNewCriterion(CareerSubjectPeer::ORIENTATION_ID, null, Criteria::ISNULL);
    $sub_orientation_criterion = $criteria->getNewCriterion(CareerSubjectPeer::SUB_ORIENTATION_ID, null, Criteria::ISNULL);

    if (!is_null($orientation = $cs->getOrientation()))
    {/*
     * Correlatives for a subject with orientation are other subjects without orientation
     * or subjects with same orientation
     */
      $criterion = $criteria->getNewCriterion(CareerSubjectPeer::ORIENTATION_ID, $orientation->getId());
      $orientation_criterion->addOr($criterion);
    }
    $criteria->add($orientation_criterion);

    if (!is_null($sub_orientation = $cs->getSubOrientation()))
    {/*
     * Correlatives for a subject with orientation are other subjects without orientation
     * or subjects with same orientation
     */
      $criterion = $criteria->getNewCriterion(CareerSubjectPeer::SUB_ORIENTATION_ID, $sub_orientation->getId());
      $sub_orientation_criterion->addOr($criterion);
    }
    $criteria->add($sub_orientation_criterion);

    if (!$exclude_related)
    {
      $related = array_map(create_function('$c', 'return $c->getCorrelativeCareerSubjectId();'), $cs->getCorrelativesRelatedByCareerSubjectId());
      $criteria->addAnd(CareerSubjectPeer::ID, $related, Criteria::NOT_IN);
    }
    return $criteria;

  }

  /**
   * Get every student that isnt inscripted in other division.
   * The inscription depends on the aproval method implemented by each school
   *
   * @param  Division     $division
   *
   * @return array Student[]
   */
  public function getAvailableStudentsForDivision(Division $division)
  {
    // Get students just inscripted in other divisions
    $not_in_criteria = new Criteria();
    $not_in_criteria->addJoin(DivisionStudentPeer::STUDENT_ID, StudentPeer::ID);
    $not_in_criteria->addJoin(DivisionStudentPeer::DIVISION_ID, DivisionPeer::ID, Criteria::INNER_JOIN);
    $not_in_criteria->add(DivisionPeer::CAREER_SCHOOL_YEAR_ID, $division->getCareerSchoolYear()->getId());
    $not_in_criteria->clearSelectColumns();
    $not_in_criteria->addSelectColumn(StudentPeer::ID);
    $stmt = StudentPeer::doSelectStmt($not_in_criteria);
    $not_in = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $students_in = array();
    foreach ($division->getCourses() as $course)
    {
      foreach ($course->getNonOptionCourseSubjects() as $course_subject)
      {
        $criteria_course = $this->getAvailableStudentsForCourseSubjectCriteria($course_subject);
        $criteria_course->clearSelectColumns();
        $criteria_course->addSelectColumn(StudentPeer::ID);
        $stmt = StudentPeer::doSelectStmt($criteria_course);
        $students_in = array_merge($stmt->fetchAll(PDO::FETCH_COLUMN), $students_in);
      }
    }

    $students_in = array_diff($students_in, $not_in);
    $c = new Criteria();
    //$c->addAnd(StudentPeer::ID,$not_in,Criteria::NOT_IN);
    $c->add(StudentPeer::ID, $students_in, Criteria::IN);

    return StudentPeer::doSelect($c);

  }

  /**
   * Get every student that can be add to a CourseSubject
   * The inscription depends on the aproval method implemented by each school
   *
   * @param  CourseSubject     $course_subject
   *
   * @return array Student[]
   */
  public function getAvailableStudentsForCourseSubject(CourseSubject $course_subject, $criteria = null, $filter_by_orientation = true)
  {
    return StudentPeer::doSelect($this->getAvailableStudentsForCourseSubjectCriteria($course_subject, $criteria, $filter_by_orientation));

  }

  /**
   * This method returns the available students for a course subject. If $filter_by_orientation == true then filter by orientation too.
   *
   * @param CourseSubject $course_subject
   * @param Criteria $criteria
   * @param Boolean $filter_by_orientation
   * @return Criteria
   */
  public function getAvailableStudentsForCourseSubjectCriteria(CourseSubject $course_subject, $criteria = null, $filter_by_orientation = true)
  {

    $criteria = is_null($criteria) ? new Criteria() : $criteria;

    //IF the course_subject is a option, the cheks are in the parent optional
    //die(var_dump($course_subject->getCareerSubjectSchoolYear()->getCareerSubject()->getIsOption()));
    $career_subject_school_year = $course_subject->getCareerSubjectSchoolYear()->getCareerSubject()->getIsOption() ? $course_subject->getCareerSubjectSchoolYear()->getOptionalCareerSubjectSchoolYear() : $course_subject->getCareerSubjectSchoolYear();
    $career_subject = $career_subject_school_year->getCareerSubject();

    //Students inscripted in the career
    $criteria->addJoin(StudentPeer::ID, CareerStudentPeer::STUDENT_ID, Criteria::INNER_JOIN);
    $criteria->addAnd(CareerStudentPeer::CAREER_ID, $career_subject->getCareerId());

    //If $filter_by_orientation == true then checks for orientation in the criteria
    if ($filter_by_orientation && !is_null($career_subject->getOrientation()))
    {
      $criteria->addJoin(CareerStudentPeer::ORIENTATION_ID, $career_subject->getOrientationId());
    }

    if ($filter_by_orientation && !is_null($career_subject->getSubOrientation()))
    {
      $criteria->addJoin(CareerStudentPeer::SUB_ORIENTATION_ID, $career_subject->getSubOrientationId());
    }


    //Students inscripted in the school_year
    $criteria->addJoin(StudentPeer::ID, SchoolYearStudentPeer::STUDENT_ID, Criteria::INNER_JOIN);
    $criteria->addAnd(SchoolYearStudentPeer::SCHOOL_YEAR_ID, $career_subject_school_year->getCareerSchoolYear()->getSchoolYearId());


    //Check if the students has the corresponds allows required to course this subject
    $criteria->addJoin(StudentPeer::ID, StudentCareerSubjectAllowedPeer::STUDENT_ID);
    $criteria->addAnd(StudentCareerSubjectAllowedPeer::CAREER_SUBJECT_ID, $career_subject->getId());

    //Criteria for eliminate students inscripted in other course_subject of the same course
    $already_criteria = new Criteria();
    $already_criteria->clearSelectColumns();
    $already_criteria->addSelectColumn(CourseSubjectStudentPeer::STUDENT_ID);
    $already_criteria->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID);
    $already_criteria->add(CourseSubjectPeer::COURSE_ID, $course_subject->getCourseId());
    $already_criteria->add(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, $course_subject->getId(), Criteria::NOT_EQUAL);
    $pdo_statement = CourseSubjectStudentPeer::doSelectStmt($already_criteria);
    $student_already_ids = $pdo_statement->fetchAll(PDO::FETCH_COLUMN);


    if (count($student_already_ids))
    {
      $criteria->addAnd(StudentPeer::ID, $student_already_ids, Criteria::NOT_IN);
    }


    return $criteria;

  }

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
  public function getAvailableStudentsForDivisionCourseSubject(CourseSubject $course_subject, $criteria = null, $filter_by_orientation = true)
  {
    return StudentPeer::doSelect($this->getAvailableStudentsForDivisionCourseSubjectCriteria($course_subject, $criteria, $filter_by_orientation));

  }

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
  public function getAvailableStudentsForDivisionCourseSubjectCriteria(CourseSubject $course_subject, $criteria = null, $filter_by_orientation = true)
  {
    $criteria = is_null($criteria) ? new Criteria() : $criteria;

    if ($course_subject->getCourse()->getDivision())
    {
      $c = new Criteria();
      $c->add(DivisionStudentPeer::DIVISION_ID, $course_subject->getCourse()->getDivisionId());
      $c->addJoin(StudentPeer::ID, DivisionStudentPeer::STUDENT_ID);
      $c->clearSelectColumns();
      $c->addSelectColumn(StudentPeer::ID);
      $stmt = StudentPeer::doSelectStmt($c);
      $in = $stmt->fetchAll(PDO::FETCH_COLUMN);
      $criteria->addAnd(StudentPeer::ID, $in, Criteria::IN);
    }

    return $this->getAvailableStudentsForCourseSubjectCriteria($course_subject, $criteria, $filter_by_orientation);

  }

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
  public function getAvailableChoicesForCareerSubjectSchoolYearCriteria(CareerSubjectSchoolYear $career_subject_school_year, $exclude_related = true, $exclude_repetead = true, PropelPDO $con = null)
  {
    /* First try to get every CareerSubject objects that are options and are being used as option, so we wont consider them */
    $career_subject = $career_subject_school_year->getCareerSubject();
    $criteria = new Criteria();
    $related = array_map(create_function('$option', 'return $option->getChoiceCareerSubjectSchoolYearId();'), $career_subject_school_year->getOptionalCareerSubjectsRelatedByCareerSubjectSchoolYearId());
    if ($exclude_repetead)
    {
      $criteria->addJoin(CareerSubjectSchoolYearPeer::ID, OptionalCareerSubjectPeer::CHOICE_CAREER_SUBJECT_SCHOOL_YEAR_ID, Criteria::INNER_JOIN);
      $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID, Criteria::INNER_JOIN);
      $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID, Criteria::INNER_JOIN);
      $criteria->addAnd(CareerSubjectPeer::IS_OPTION, true);
      $criteria->addAnd(CareerSubjectPeer::YEAR, $career_subject->getYear());
      $criteria->addAnd(CareerSubjectPeer::CAREER_ID, $career_subject->getCareerId());
      $criteria->addAnd(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $career_subject_school_year->getSchoolYear()->getId());

      //At this point discard related options to career_subject_school_year

      $criteria->addAnd(CareerSubjectSchoolYearPeer::ID, $related, Criteria::NOT_IN);

      $options_used = CareerSubjectSchoolYearPeer::doSelect($criteria);

      $not_to_consider = array_map(create_function('$cssy', 'return $cssy->getId();'), $options_used);
    }


    // Now try to see which career subjects are available
    $criteria->clear();

    $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID, Criteria::INNER_JOIN);
    $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID, Criteria::INNER_JOIN);

    $criteria->addAnd(CareerSubjectPeer::IS_OPTION, true);
    $criteria->addAnd(CareerSubjectPeer::YEAR, $career_subject->getYear());
    $criteria->addAnd(CareerSubjectPeer::CAREER_ID, $career_subject->getCareerId());
    // Dont forget to consider orientation
    $criteria->addAnd(CareerSubjectPeer::ORIENTATION_ID, $career_subject->getOrientationId());
    $criteria->addAnd(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $career_subject_school_year->getSchoolYear()->getId());

    //Excludes the optional related of the excluded_ids. This is for double list that needs the owns related
    if ($exclude_related)
    {
      $criteria->addAnd(CareerSubjectSchoolYearPeer::ID, $related, Criteria::NOT_IN);
    }

    // Don't consider objects related to other optional career subject!
    if ($exclude_repetead)
    {
      $criteria->addAnd(CareerSubjectSchoolYearPeer::ID, $not_to_consider, Criteria::NOT_IN);
    }

    return $criteria;

  }

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
  public function getAvailableChoicesForCareerSubjectSchoolYear(CareerSubjectSchoolYear $career_subject_school_year, $exclude_related = true, PropelPDO $con = null)
  {
    $criteria = $this->getAvailableChoicesForCareerSubjectsSchoolYearCriteria($career_subject_school_year, $exclude_related, $con);
    return CareerSubjectSchoolYearPeer::doSelect($criteria);

  }

  /**
   * Returns those CareerSubjects that are correlative for $career_subject. This depends on
   * has_correlative_previous_year flag
   *
   * @param CareerSubject $career_subject
   * @param Criteria $criteria add custom filters to returned values
   * @param PropelPDO $con
   * @return array CareerSubject[]
   */
  public function getCorrelativesForCareerSubject(CareerSubject $career_subject, Criteria $criteria = null, PropelPDO $con = null)
  {
    if ($career_subject->getHasCorrelativePreviousYear())
    {
      // $criteria must filter only the CareerSubject objects that belong to the
      // same Career as $career_subject, different from $career_subject, that
      // are in an year up to the same year as $career_subject
      $criteria = is_null($criteria) ? new Criteria() : $criteria;
      $criteria->add(CareerSubjectPeer::ID, $career_subject->getId(), Criteria::NOT_EQUAL);
      $criteria->add(CareerSubjectPeer::CAREER_ID, $career_subject->getCareerId());
      $criteria->add(CareerSubjectPeer::YEAR, $career_subject->getYear() - 1); //Only consider previous year
      // Dont forget to consider orientation
      $orientation_criterion = $criteria->getNewCriterion(CareerSubjectPeer::ORIENTATION_ID, null, Criteria::ISNULL);

      $sub_orientation_criterion = $criteria->getNewCriterion(CareerSubjectPeer::SUB_ORIENTATION_ID, null, Criteria::ISNULL);

      if (!is_null($orientation = $career_subject->getOrientation()))
      {/*
       * Correlatives for a subject with orientation are other subjects without orientation
       * or subjects with same orientation
       */
        $criterion = $criteria->getNewCriterion(CareerSubjectPeer::ORIENTATION_ID, $orientation->getId());
        $orientation_criterion->addOr($criterion);
      }
      $criteria->add($orientation_criterion);

      if (!is_null($sub_orientation = $career_subject->getSubOrientation()))
      {/*
       * Correlatives for a subject with orientation are other subjects without orientation
       * or subjects with same orientation
       */
        $criterion = $criteria->getNewCriterion(CareerSubjectPeer::ORIENTATION_ID, $orientation->getId());
        $sub_orientation_criterion->addOr($criterion);
      }
      $criteria->add($sub_orientation_criterion);

      return CareerSubjectPeer::doSelect($criteria);
    }

    return array_map(create_function('$correlative', 'return $correlative->getCareerSubjectRelatedByCorrelativeCareerSubjectId();'), $career_subject->getCorrelativesRelatedByCareerSubjectId($criteria));

  }

  /**
   * Returns an array of CareerSubjects that has $career_subject as their correlative
   *
   * @param CareerSubject $career_subject
   * @param Criteria $criteria
   * @param PropelPDO $con
   * @return array CareerSubject[]
   */
  public function getCareerSubjectCorrelativesOf(CareerSubject $career_subject, Criteria $criteria = null, PropelPDO $con = null)
  {
    if ($career_subject->getIsChoice())
      return array(); //If choice it can't have correlatives

    /* Criteria used for retrieveing custom correlatives */
    $criteria1 = is_null($criteria) ? new Criteria() : $criteria;
    // Join CareerSubject with Correlatives for those cases where has_correlative_previous_year is false
    $criteria1->addJoin(CareerSubjectPeer::ID, CorrelativePeer::CAREER_SUBJECT_ID, Criteria::INNER_JOIN);
    $criteria1->addAnd(CareerSubjectPeer::CAREER_ID, $career_subject->getCareerId());
    $criteria1->addAnd(CareerSubjectPeer::ID, $career_subject->getId(), Criteria::NOT_EQUAL);
    $criteria1->addAnd(CorrelativePeer::CORRELATIVE_CAREER_SUBJECT_ID, $career_subject->getId());
    $criteria1->addAnd(CareerSubjectPeer::HAS_CORRELATIVE_PREVIOUS_YEAR, 0);

    $custom_correlatives_ids = array_map(create_function('$rs', 'return $rs->getId();'), CareerSubjectPeer::doSelect($criteria1));

    /* Criteria used for retrieving calculated correlatives by HAS_CORRELATIVE_PREVIOUS_YEAR */
    $criteria2 = is_null($criteria) ? new Criteria() : $criteria;
    // Fetch those subjects with year gerater than career_subject->getYear()
    $criteria2->addAnd(CareerSubjectPeer::YEAR, $career_subject->getYear() + 1); //Only consider next year career_subjects
    $criteria2->addAnd(CareerSubjectPeer::HAS_CORRELATIVE_PREVIOUS_YEAR, 1);
    $criteria2->addAnd(CareerSubjectPeer::CAREER_ID, $career_subject->getCareerId());
    $criteria2->addAnd(CareerSubjectPeer::ID, $career_subject->getId(), Criteria::NOT_EQUAL);
    if (!is_null($orientation = $career_subject->getOrientation()))
    {
      /* If career_subject is orientation, then correlatives are only career_subjects
       *  with the same orientation
       */
      $criteria2->addAnd(CareerSubjectPeer::ORIENTATION_ID, $orientation->getId());
    }

    if (!is_null($sub_orientation = $career_subject->getSubOrientation()))
    {
      /* If career_subject is orientation, then correlatives are only career_subjects
       *  with the same orientation
       */
      $criteria2->addAnd(CareerSubjectPeer::SUB_ORIENTATION_ID, $sub_orientation->getId());
    }

    $calculated_correlatives_ids = array_map(create_function('$rs', 'return $rs->getId();'), CareerSubjectPeer::doSelect($criteria2));

    $c = new Criteria();
    $c->add(CareerSubjectPeer::ID, array_merge($calculated_correlatives_ids, $custom_correlatives_ids), Criteria::IN);
    return CareerSubjectPeer::doSelect($c);

  }

  /**
   * Returns the number that represents the first year for any career in this school
   * @return int the first year (Default 1)
   */
  public function getMinimumCareerYear()
  {
    return 1;

  }

  /**
   * ******************************************************************************
   * ******************************************************************************
   *
   * The following methods are for Student assignement to related entities
   *
   * ******************************************************************************
   * ******************************************************************************
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
  public function getAvailableCareerForStudentCriteria(Student $student, $exclude_related = true, Criteria $criteria = null, PropelPDO $con = null)
  {
    $criteria = is_null($criteria) ? new Criteria() : $criteria;

    $criteria->addJoin(CareerPeer::ID, CareerSchoolYearPeer::CAREER_ID);
    $criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    $criteria->add(CareerSchoolYearPeer::IS_PROCESSED, false);
    if ($exclude_related)
    {
      $related = array_map(create_function('$c', 'return $c->getCareerId();'), $student->getCareerStudents());
      $criteria->addAnd(CareerPeer::ID, $related, Criteria::NOT_IN);
    }
    return $criteria;

  }

  /**
   * Returns the first weekday that a course can be coursed.
   * 1 (monday) through 7 (sunday)
   *
   * @return integer
   */
  public function getFirstCourseSubjectWeekday()
  {
    return 1;

  }

  /**
   * Returns the last weekday that a course can be coursed.
   * 1 (monday) through 7 (sunday)
   *
   * @return integer
   */
  public function getLastCourseSubjectWeekday()
  {
    return 5;

  }

  /**
   * Returns the valid hours for a course
   *
   * @return array
   */
  public function getHoursArrayForSubjectWeekday()
  {
    return $this->HOUR_FOR_SCHEMA;

  }

  /**
   * Returns the valid hours for a course
   *
   * @return array
   */
  public function getMinutesArrayForSubjectWeekday()
  {
    return $this->MINUTES_FOR_SCHEMA;

  }

  /**
   * Returns the available students for the given examination repproved subject.
   *
   * @param ExaminationRepprovedSubject $examination_repproved_subject
   * @return array
   */
  public function getAvailableStudentsForExaminationRepprovedSubject(ExaminationRepprovedSubject $examination_repproved_subject)
  {
    $c = new Criteria();
    $c->add(StudentRepprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNULL);
    $c->addJoin(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, $examination_repproved_subject->getCareerSubjectId());

    return StudentRepprovedCourseSubjectPeer::doSelect($c);

  }

  public function getMenuYaml()
  {
    return sfConfig::get('sf_app_config_dir') . '/menu.yml';

  }

  public function getListObjectActionsForSchoolYear()
  {
    return array('change_state' => array('action' => 'changeState', 'condition' => 'canChangedState', 'label' => 'Cambiar vigencia', 'credentials' => array(0 => 'edit_school_year',),), 'registered_students' => array('action' => 'registeredStudents', 'credentials' => array(0 => 'show_school_year',), 'label' => 'Registered students',), 'careers' => array('action' => 'schoolYearCareers', 'label' => 'Ver carreras', 'credentials' => array(0 => 'show_career',),), 'examinations' => array('action' => 'examinations', 'label' => 'Examinations', 'credentials' => array(0 => 'show_examination',), 'condition' => 'canExamination',), 'examination_repproved' => array('action' => 'examinationRepproved', 'label' => 'Examination repproved', 'credentials' => array(0 => 'show_examination_repproved',), 'condition' => 'canExamination',), 'final_examination' => array('action' => 'finalExamination', 'label' => 'Final examination', 'credentials' => array(0 => 'show_final_examination',), 'condition' => 'getIsActive',), '_delete' => array('credentials' => array(0 => 'edit_school_year',), 'condition' => 'canBeDeleted',),);

  }

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
  public function createStudentCareerSubjectAlloweds(CareerStudent $career_student, $start_year, PropelPDO $con)
  {
    $career_student->createStudentsCareerSubjectAlloweds($start_year, $con);

  }

  /**
   * this method returns true when the attendance type is subject_attendance
   *
   * @return boolean
   */
  public function hasSubjectAttendance()
  {
    return in_array('Por materia', $this->getAttendanceTypeChoices());

  }

  public function getAttendanceTypeChoices()
  {
    return $this->attendance_types;

  }

  public function getAttendanceTypeFor($object)
  {
    return ($object instanceof Division) ? self::ATTENDANCE_DAY : self::ATTENDANCE_SUBJECT;

  }

  /**
   * This method returns a Criteria with the career_subjects that can be passed by equivalence of the student.
   *
   * @param integer $career_id
   * @param Student $student
   *
   * @return Criteria $c
   */
  public function getCareerSubjectsForEquivalenceCriteria($career_id, Student $student)
  {
    $c = new Criteria();
    //Quito las materias ya approbadas por el estudiante
    $c->add(StudentApprovedCareerSubjectPeer::STUDENT_ID, $student->getId());
    $c->clearSelectColumns();
    $c->addSelectColumn(StudentApprovedCareerSubjectPeer::CAREER_SUBJECT_ID);
    $pdo = StudentApprovedCareerSubjectPeer::doSelectStmt($c);
    $already_approved_ids = $pdo->fetchAll(PDO::FETCH_COLUMN);

    //Ahora chequeo que materias puede rendir.
    $c = new Criteria();
    $c->add(CareerSubjectPeer::CAREER_ID, $career_id);
    $c->add(CareerSubjectPeer::ID, $already_approved_ids, Criteria::NOT_IN);
    $career_subjects = CareerSubjectPeer::doSelect($c);

    $career_subject_available = array();
    foreach ($career_subjects as $career_subject)
    {
      if ($this->canAddEquivalenceFor($career_subject, $student))
        $career_subject_available[] = $career_subject->getId();
    }

    $c = new Criteria();
    $c->add(CareerSubjectPeer::CAREER_ID, $career_id);
    $c->add(CareerSubjectPeer::ID, $career_subject_available, Criteria::IN);

    return $c;

  }

  /**
   * This method check if the student has approved all the correlatives for the careerSubject.
   *
   * @param CareerSubject $career_subject
   * @param Student $student
   * @return boolean
   */
  public function canAddEquivalenceFor(CareerSubject $career_subject, Student $student)
  {
    foreach ($career_subject->getCorrelativeCareerSubjects() as $cs)
    {
      $c = new Criteria();
      $c->add(StudentApprovedCareerSubjectPeer::STUDENT_ID, $student->getId());
      $c->add(StudentApprovedCareerSubjectPeer::CAREER_SUBJECT_ID, $cs->getId());

      if (StudentApprovedCareerSubjectPeer::doCount($c) == 0)
      {
        return false;
      }
    }

    return true;

  }

  public function getAttendanceDay()
  {
    return self::ATTENDANCE_DAY;

  }

  public function getAttendanceSubject()
  {
    return self::ATTENDANCE_SUBJECT;

  }

  /**
   * Return the title for the exportation in shared_course_subject
   *
   * @param Teacher $teacher
   * @return string
   */
  public function getExportationSharedCourseSubjectTitle(Teacher $teacher)
  {
    return 'Cursos de ' . $teacher;

  }

  /**
   * This method filters students with preceptor in the AdminGeneratorFilter
   *
   * @param Criteria $criteria
   * @param integer $user_id
   */
  public function joinPreceptorWithStudents($criteria, $user_id)
  {
    return PersonalPeer::joinWithStudents($criteria, $user_id);

  }

  public function getPrintReportUrlFor(Division $division)
  {
    return 'Report::Kimkelen_DEMO/boletin-trimestral-asistencia-por-materia.prpt';

  }

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
  public function getAbsences($career_school_year_id, $student_id, $period = null, $course_subject_id = null)
  {
    $c = new Criteria();
    $c->add(StudentAttendancePeer::STUDENT_ID, $student_id);
    $c->add(StudentAttendancePeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);

    if (!is_null($course_subject_id))
    {
      if ($course_subject_id instanceof CourseSubject)
      {
        $c->add(StudentAttendancePeer::COURSE_SUBJECT_ID, $course_subject_id->getId());
      }
      else
      {
        $c->add(StudentAttendancePeer::COURSE_SUBJECT_ID, $course_subject_id);
      }
    }

    $c->add(StudentAttendancePeer::VALUE, 0, Criteria::NOT_EQUAL);

    if (!is_null($period))
    {
      $criterion = $c->getNewCriterion(StudentAttendancePeer::DAY, $period->getStartAt(), Criteria::GREATER_EQUAL);
      $criterion->addAnd($c->getNewCriterion(StudentAttendancePeer::DAY, $period->getEndAt(), Criteria::LESS_EQUAL));
      $c->add($criterion);
    }
    return $student_attendances = StudentAttendancePeer::doSelect($c);

  }

  /**
   * This method returns 'final_mark' if the course type is bimester/quaterly and the mark is 3
   *
   * @param type $mark
   * @param type $course_type
   *
   * return String dependending on the mark
   */
  public function getMarkTitle($mark, $course_type = null)
  {
    return 'Mark %number%';

  }

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
  public function isFreeStudent(Student $student, CareerSchoolYearPeriod $career_school_year_period = null, CourseSubject $course_subject = null, CareerSchoolYear $career_school_year)
  {
    $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $career_school_year);
    $student_free = StudentFreePeer::retrieveByStudentCareerSchoolYearCareerSchoolYearPeriodAndCourse($student_career_school_year, $career_school_year_period, $course_subject);

    return is_null($student_free) ? false : $student_free->getIsFree();
  }

  public function getFreeLabel(CourseSubjectStudentMark $course_subject_student_mark)
  {

    return 'Free';
  }

  public function getShortFreeLabel(CourseSubjectStudentMark $course_subject_student_mark)
  {

    return 'L';
  }

  public function getCareerSubjectSchoolYearSpecialIds($year)
  {

    return array();
  }

  public function getFormattedAssistanceValue($student_attendance)
  {
    switch ($student_attendance->getValue())
    {
      case 0:
        return '·';
        break;

      case 1:
        return 'A';
        break;

      default:
        return $student_attendance->getValueString();
        break;
    }
  }

  public function getAvailableStudentsForExaminationSubject(ExaminationSubject $examination_subject)
  {
    $c = new Criteria();
    $c->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $examination_subject->getCareerSubjectSchoolYearId());
    $c->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID, Criteria::INNER_JOIN);
    $c->addJoin(CourseSubjectStudentPeer::ID, StudentDisapprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, Criteria::INNER_JOIN);
    $c->add(CourseSubjectStudentPeer::STUDENT_APPROVED_COURSE_SUBJECT_ID, null, Criteria::ISNULL);
    $c->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);

    $c->addJoin(CourseSubjectStudentPeer::ID, CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, Criteria::INNER_JOIN);
    $c->add(CourseSubjectStudentExaminationPeer::EXAMINATION_NUMBER, $examination_subject->getExamination()->getExaminationNumber());

    $c->add(CourseSubjectStudentExaminationPeer::IS_ABSENT, false);
    $c->addAnd(CourseSubjectStudentExaminationPeer::MARK, null, Criteria::ISNULL);

    //Quito los que ya la aprobaron
    $approved_criteria = new Criteria();
    $approved_criteria->addJoin(StudentApprovedCareerSubjectPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
    $approved_criteria->add(StudentApprovedCareerSubjectPeer::CAREER_SUBJECT_ID, $examination_subject->getCareerSubjectSchoolYear()->getCareerSubjectId());
    $approved_criteria->clearSelectColumns();
    $approved_criteria->addSelectColumn(StudentApprovedCareerSubjectPeer::STUDENT_ID);
    $stmt = StudentApprovedCareerSubjectPeer::doSelectStmt($approved_criteria);
    $not_in = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $c->add(StudentPeer::ID, $not_in, Criteria::NOT_IN);

    return StudentPeer::doSelect($c);

  }

  public function getAvailableStudentsForManualExaminationSubject(ExaminationSubject $examination_subject)
  {
    $c = new Criteria();
    $c->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $examination_subject->getCareerSubjectSchoolYearId());
    $c->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID, Criteria::INNER_JOIN);
    $c->add(CourseSubjectStudentPeer::STUDENT_APPROVED_COURSE_SUBJECT_ID, null, Criteria::ISNULL);
    $c->addJoin(CourseSubjectStudentPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
    $c->addJoin(CourseSubjectStudentPeer::ID, StudentDisapprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, Criteria::INNER_JOIN);
    $c->add(StudentDisapprovedCourseSubjectPeer::EXAMINATION_NUMBER, $examination_subject->getExamination()->getExaminationNumber());

    return StudentPeer::doSelect($c);
  }

  public function getMarkNameByNumberAndCourseType($number, $course_type)
  {
    return $number . 'T';

  }

  /*
   * This method returns the choices available for the current configuration.
   *
   * @see CourseType->getOptions()
   *
   * @return integer
   */

  public function getCourseTypeChoices()
  {
    return $this->_course_type_options;

  }

  public function getDefaultCourseType()
  {
    return CourseType::TRIMESTER;

  }

  public function getSubjectToString($subject)
  {
    return $subject->getName();

  }

  public function getCourseSubjectStudentsForCourseTypeAndAttendanceForSubject($student, $course_type, $school_year = null)
  {
    $course_subject_students = $this->getCourseSubjectStudentsForCourseTypeArray($student, $course_type, $school_year);
    $results = array();
    foreach ($course_subject_students as $css)
    {
      if ($css->getCourseSubject()->getCourseType() == $course_type && $css->getCourseSubject()->getCourse()->hasAttendanceForSubject())
      {
        $results[] = $css;
      }
    }

    return $results;

  }

  public function getCourseSubjectStudentsForCourseTypeAndAttendanceForDay($student, $course_type, $school_year = null)
  {
    $course_subject_students = $this->getCourseSubjectStudentsForCourseTypeArray($student, $course_type, $school_year);
    $results = array();

    foreach ($course_subject_students as $css)
    {

      if ($css->getCourseSubject()->getCourseType() == $course_type && $css->getCourseSubject()->getCourse()->hasAttendanceForDay())
      {
        $results[] = $css;
      }
    }

    return $results;

  }

  public function getCourseSubjectStudentsForCourseType($student, $course_type, $school_year = null)
  {
    $course_subject_students = $this->getCourseSubjectStudentsForCourseTypeArray($student, $course_type, $school_year);
    $results = array();

    if (!is_array($course_type))
    {
      $course_type = array($course_type);
    }

    foreach ($course_subject_students as $css)
    {
      if (in_array($css->getCourseSubject()->getCourseType(), $course_type))
      {
        $results[] = $css;
      }
    }

    return $results;

  }

  public function getCourseSubjectStudentsForCourseTypeArray($student, $course_type = null, $school_year = null)
  {
    if (is_null($school_year))
    {	
      $school_year = SchoolYearPeer::retrieveCurrent();
    }

    $c = new Criteria();
    $c->add(CoursePeer::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->add(CourseSubjectStudentPeer::IS_NOT_AVERAGEABLE, false);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    CareerSubjectSchoolYearPeer::sorted($c);
	
    return $student->getCourseSubjectStudents($c);

  }

  public function getCourseSubjectStudentsForAnalytics($student, $school_year)
  {
    
    $ret = array();

    foreach ($this->getCourseSubjectStudentsForCourseTypeArray($student, null,$school_year) as $css){
      $ret[] = $this->getInstanceSubjectStudentAnalytic($css);
    }
    return $ret;
  }

  protected function getInstanceSubjectStudentAnalytic($course_subject_student){

    $klass = $this->getClassSubjectStudentAnalytic();

    return new $klass($course_subject_student);

  }

  protected function getClassSubjectStudentAnalytic(){

    return 'BaseSubjectStudentAnalytic';

  }

  public function getDaysForMultipleAttendanceForm()
  {
    return self::DAYS_FOR_MULTIPLE_ATTENDANCE_FORM;

  }

  public function showReportCardRepproveds()
  {
    return true;

  }

  public function getTotalAbsences($career_school_year_id, $period, $course_subject_id = null, $exclude_justificated = true, $student_id)
  {
    $absences = $this->getAbsences($career_school_year_id, $student_id, $period, $course_subject_id);
    $rounder  = new StudentAttendanceRounder();
    $total    = 0;

    foreach ($absences as $absence)
    {
      // sacamos las justificadas, es decir se quiere el total SIN las justificadas
      if ($exclude_justificated && $absence->hasJustification())
      {
        continue;
      }

      $total += $absence->getValue();
      $rounder->process($absence);
    }

    $diff = $rounder->calculateDiff();

    return $total + $diff;
  }

  public function getSchoolName()
  {
    return $this->school_name;

  }

  public function __toString()
  {
    return $this->school_name;

  }

  public function getMarksForCourseType($mark_number)
  {
    return BaseCustomOptionsHolder::getInstance('CourseType')->getMarksFor($mark_number);

  }

  public function getBlocksPerCourseSubjectDay()
  {
    return self::BLOCKS_PER_COURSE_SUBJECT_DAY;

  }

  public function getDivisionCourseType()
  {
    return CourseType::TRIMESTER;

  }

  public function canRelatedToDivision($division = null, $is_current_school_year = null)
  {
    return false;
  }

	public function canShowCBFE() {
		return false;
	}

}
