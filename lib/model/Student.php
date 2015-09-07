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

class Student extends BaseStudent
{

  /**
   * Proxies getPerson()->$method as getPersonMethod in current object. Only for getters
   *
   * @param string $method
   * @param <type> $arguments
   * @return <type>
   */
  public function __call($method, $arguments)
  {
    if (preg_match('/^getPerson(.*)/', $method, $matches) && isset($matches[1]))
    {
      $method = "get" . $matches[1];
      return $this->getPerson()->$method();
    }
    if (preg_match('/^canPersonBe(.*)/', $method, $matches) && isset($matches[1]))
    {
      $method = "canBe" . $matches[1];
      return $this->getPerson()->$method();
    }
    parent::__call($method, $arguments);

  }

  public function __toString()
  {
    return $this->getPersonFullname();

  }

  /**
   * Returns a boolean indicating if object is registered in receiving school year $csy.
   * If $csy is null, then check for current school year
   *
   * @return boolean
   */
  public function getIsRegistered(SchoolYear $csy = null)
  {
    if (is_null($csy))
    {
      $csy = SchoolYearPeer::retrieveCurrent();
    }
    $criteria = new Criteria();
    $criteria->addAnd(SchoolYearStudentPeer::SCHOOL_YEAR_ID, $csy->getId());
    return $this->countSchoolYearStudents($criteria) == 1;

  }

  /**
   * Returns the SchoolYearStudent instance corresponding to receiving schoolYear $csy.
   * If $csy is null, then returns the instance for current SchoolYear. If none return null
   *
   * @param SchoolYear $csy
   * @return SchoolYearStudent
   */
  public function getSchoolYearStudentForSchoolYear(SchoolYear $csy = null)
  {
    if (is_null($csy))
    {
      $csy = SchoolYearPeer::retrieveCurrent();
    }
    $criteria = new Criteria();
    $criteria->addAnd(SchoolYearStudentPeer::SCHOOL_YEAR_ID, $csy->getId());
    $array = $this->getSchoolYearStudents($criteria);
    return array_shift($array);

  }

  /**
   * Opposite of getIsRegistered.
   *
   * @see getIsRegistered
   * @return boolean
   */
  public function getIsNotRegistered()
  {
    return !$this->getIsRegistered();

  }

  /**
   * Returns if this student can be managed for StudentCareerSubjectAllowed entity
   * By default returns true if student is registered for at least one career
   */
  public function canBeManagedForCareerSubjectAllowed()
  {
    return $this->countCareerStudents() > 0;

  }

  /**
   * Returns if the student is registered to $career.
   *
   * @param Career $career
   * @return boolean
   */
  public function isRegisteredToCareer(Career $career, $con = null)
  {
    if ($con == null)
    {
      $con = Propel::getConnection(StudentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    $c = new Criteria();
    $c->add(CareerStudentPeer::CAREER_ID, $career->getId());

    return $this->countCareerStudents($c, $con) > 0;

  }

  /**
   * Registers the student for the given career.
   *
   * @param Career $career
   * @param Orientation $orientation
   * @param integer $start_year
   */
  public function registerToCareer(Career $career, Orientation $orientation = null, SubOrientation $sub_orientation = null, $start_year, $con = null)
  {
    if ($con == null)
    {
      $con = Propel::getConnection(StudentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    $career_student = new CareerStudent();
    $career_student->setCareerId($career->getId());

    if ($orientation)
      $career_student->setOrientationId($orientation->getId());

    if ($sub_orientation)
      $career_student->setSubOrientationId($sub_orientation->getId());

    $career_student->setStudentId($this->getId());
    $career_student->setStartYear($start_year);
    SchoolBehaviourFactory::getInstance()->setStudentFileNumberForCareer($career_student, $con);

    $career_student->save($con);

    SchoolBehaviourFactory::getInstance()->createStudentCareerSubjectAlloweds($career_student, $start_year, $con);

  }

  /**
   * Registers the student to the given $school_year in the given $shift.
   *
   * @param SchoolYear $school_year
   * @param Shift $shift
   * @param mixed $con
   * @return void
   */
  public function registerToSchoolYear($school_year, $shift, $con = null)
  {
    if ($con == null)
    {
      $con = Propel::getConnection(StudentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    $school_year_student = new SchoolYearStudent();
    $school_year_student->setStudent($this);
    $school_year_student->setSchoolYear($school_year);
    $school_year_student->setShift($shift);
    $school_year_student->save($con);

    $school_year_student->clearAllReferences(true);
    unset($school_year_student);

  }

  /*
   * This method returns the file number, depends of the school behaiv
   *
   */

  public function getFileNumber(Career $career)
  {
    if (SchoolBehaviourFactory::getInstance()->getFileNumberIsGlobal())
    {
      $file_number = $this->getGlobalFileNumber();
    }
    else
    {
      $c = new Criteria();
      $c->add(CareerStudentPeer::CAREER_ID, $career->getId());
      $c->add(CareerStudentPeer::STUDENT_ID, $this->getId());

      $file_number = CareerStudentPeer::doSelectOne($c)->getFileNumber();
    }
    return sprintf("%05s", $file_number);

  }

  public function getMessageCantBeManagedForCareerSubjectAllowed()
  {
    return "The student must be registered to career before you can manage allowed subjects.";

  }

  public function getMessageCantPersonBeActivated()
  {
    return "The student cant be activated because is already active.";

  }

  public function getMessageCantGenerateReport()
  {
    return "The report can't be generated because student is not currently inscripted in a division.";

  }

  public function getMessageCantBeDeactivated()
  {
    return "The student cant be deactivated because is already inactive or is matriculed.";

  }

  public function closeCareerSchoolYear(CareerSchoolYear $career_school_year, PropelPDO $con = null)
  {
    SchoolBehaviourFactory::getEvaluatorInstance()->evaluateCareerSchoolYearStudent($career_school_year, $this, $con);
  }

  /**
   * This methods returns the year of the careerSchoolYear of the student.
   * @param CareerSchoolYear $career_school_year
   * @return integer
   */
  public function getCareerYear(CareerSchoolYear $career_school_year)
  {
    $c = new Criteria();
    $c->add(StudentCareerSchoolYearPeer::STUDENT_ID, $this->getId());
    $c->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::YEAR);
    $scsy = StudentCareerSchoolYearPeer::doSelectOne($c);
    return is_null($scsy) ? 1 : $scsy->getYear();

  }

  /**
   * Returns the number of disapproved for the given school year.
   *
   * @param SchoolYear $school_year
   * @return integer the disapproved count
   */
  public function countDisapprovedForSchoolYear(SchoolYear $school_year, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    $c = new Criteria();
    $c->addJoin(StudentDisapprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $this->getId());

    $previous_ids = array_map(create_function('$c', 'return $c->getId();'), CourseSubjectStudentPeer::doSelect($c));

    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $this->getId());
    $c->add(CoursePeer::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addJoin(CoursePeer::ID, CourseSubjectPeer::COURSE_ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::ID, StudentDisapprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID);
    $c->add(StudentDisapprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNULL);
    $c->add(StudentDisapprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, $previous_ids, Criteria::NOT_IN);

    return StudentDisapprovedCourseSubjectPeer::doCount($c, $con);

  }

  /**
   * Este metodo retorna las posibes careerSubjects que el estudiante aprobo en el final.
   * Esto es por el caso de que un estudiante de un final de una materia que tiene en dos carreras.
   *
   * @param FinalExaminationSubject $final_examination_subject
   * @return array CareerSubject[]
   */
  public function getCareerSubjectForFinalExaminationSubject(FinalExaminationSubject $final_examination_subject)
  {
    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $this->getId());
    $c->add(CourseSubjectStudentPeer::STUDENT_APPROVED_COURSE_SUBJECT_ID, null, Criteria::ISNOTNULL);
    $c->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
    $c->addJoin(CareerSubjectPeer::SUBJECT_ID, $final_examination_subject->getSubjectId());
    return CareerSubjectPeer::doSelectOne($c);

  }

  /**
   * This method check if the student has approved all courses for each career_subject received
   *
   * @param CareerSubject[] $career_subjects
   * @return boolean
   */
  public function hasApprovedAllCareerSubejcts($career_subjects)
  {
    foreach ($career_subjects as $career_subject)
    {
      if (!$this->hasApprovedCareerSubject($career_subject))
        return false;
    }

    return true;

  }

  public function hasApprovedCareerSubject($career_subject, PropelPDO $con = null)
  {
    $c = new Criteria();
    $c->add(StudentApprovedCourseSubjectPeer::STUDENT_ID, $this->getID());
    $c->addJoin(StudentApprovedCourseSubjectPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID);
    $c->add(CareerSubjectPeer::ID, $career_subject->getId());

    return StudentApprovedCourseSubjectPeer::doCount($c, false, $con);

  }

  /**
   * This method returns true if a student is inscripted at least one career.
   *
   * @param PropelPDO $con
   * @return boolean
   */
  public function canManageEquivalence(PropelPDO $con = null)
  {
    return $this->countCareerStudents();

  }

  public function getMessageCantManageEquivalence()
  {
    return 'The student is not inscripted in at least one career';

  }

  public function canManageRegularity()
  {
    return $this->countCourseSubjectStudents();

  }

  public function getAbsences($career_school_year_id, $period = null, $course_subject_id = null, $exclude_justificated = false)
  {

    return SchoolBehaviourFactory::getInstance()->getAbsences($career_school_year_id, $this->getId(), $period, $course_subject_id, $exclude_justificated);

  }

  public function getTotalAbsences($career_school_year_id, $period, $course_subject_id = null, $exclude_justificated = true)
  {

    return SchoolBehaviourFactory::getInstance()->getTotalAbsences($career_school_year_id, $period, $course_subject_id, $exclude_justificated, $this->getId());

  }

  public function getTotalJustificatedAbsences($career_school_year_id, $period, $course_subject_id = null)
  {

    return $this->getTotalAbsences($career_school_year_id, $period, $course_subject_id, false) - $this->getTotalAbsences($career_school_year_id, $period, $course_subject_id, true);

  }

  public function getRemainingAbsenceFor(CareerSchoolYearPeriod $period = null, $course_subject = null, $exclude_justificated = true, $career_school_year, $divison = null)
  {
    $min_max_absence = 0;

    $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($this, $career_school_year);


    if (is_null($course_subject) && is_null($divison))
    {
      //ME QUEDO CON LA CONFIGURACION MINIMA PARA ESE PERIODO, en caso de que este anotado en mas de una división
      $max_absence = $student_career_school_year->getMaxAbsenceForPeriod($period);
    }
    elseif (!is_null($divison))
    {

      $configuration = CourseSubjectConfigurationPeer::retrieveByDivisionAndPeriod($divison->getId(), $period->getId());

//      Si la division no tiene el maximo de  asistencias permitidas se lo  pido ala configuracion del anio
      if (is_null($configuration))
      {
        $max_absence = $career_school_year->getMaxAbsenceInYear($divison->getYear());
      }
      else
      {
        $max_absence = $configuration->getMaxAbsence();
      }
    }
    else
    {

      $max_absence = $course_subject->getMaxAbsenceForPeriod($period);
    }

    $total_absences = $this->getTotalAbsences($career_school_year->getId(), $period, $course_subject, $exclude_justificated);

    return $max_absence - $total_absences;

  }

  public function getCurrentDivisions($career_school_year_id = null)
  {
    $c = new Criteria();

    if (is_null($career_school_year_id))
    {
      $school_year = SchoolYearPeer::retrieveCurrent();

      $c->addJoin(DivisionPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
      $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $school_year->getId());
    }
    else
    {
      $c->add(DivisionPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    }

    $c->add(DivisionStudentPeer::STUDENT_ID, $this->getId());
    $c->addJoin(DivisionPeer::ID, DivisionStudentPeer::DIVISION_ID);

    return DivisionPeer::doSelect($c);

  }

  public function getCurrentDivisionsString($career_school_year_id = null)
  {
    return implode(' ', $this->getCurrentDivisions($career_school_year_id));

  }

  public function getTotalReincorporated(CareerSchoolYearPeriod $period, $course_subject_id = null)
  {
    $c = new Criteria();
    $c->add(StudentReincorporationPeer::CAREER_SCHOOL_YEAR_PERIOD_ID, $period->getId());
    $c->add(StudentReincorporationPeer::STUDENT_ID, $this->getId());
    $c->add(StudentReincorporationPeer::COURSE_SUBJECT_ID, $course_subject_id);

    $reincorporated = 0;
    foreach (StudentReincorporationPeer::doSelect($c) as $sr)
    {
      $reincorporated += $sr->getReincorporationDays();
    }
    return $reincorporated;

  }

  public function hasReincorporations($career_school_year_id, $course_subject_id = null)
  {
    $c = new Criteria();
    $c->add(StudentReincorporationPeer::STUDENT_ID, $this->getId());
    if (is_null($course_subject_id))
    {
      $c->add(StudentReincorporationPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year_id);
    }
    else
    {
      $c->add(StudentReincorporationPeer::COURSE_SUBJECT_ID, $course_subject_id);
    }

    return StudentReincorporationPeer::doCount($c);

  }

  public function isAlmostFree(CareerSchoolYearPeriod $career_school_year_period = null, $course_subject = null, $career_school_year = null, $divison = null)
  {
//    var_dump($career_school_year);
    return ($this->getRemainingAbsenceFor($career_school_year_period, $course_subject, true, $career_school_year, $divison) < 2);

  }

  public function getFreeClass(CareerSchoolYearPeriod $career_school_year_period = null, $course_subject = null, $career_school_year, $divison = null)
  {
    if ($this->isFree($career_school_year_period, $course_subject, $career_school_year))
    {

      return 'free';
    }
    else
    {
      if ($this->isAlmostFree($career_school_year_period, $course_subject, $career_school_year, $divison))
      {
        return 'almost_free';
      }
    }

    return 'attendance_regular';

  }

  public function getBrothers()
  {
    $brothers = array();
    foreach ($this->getBrotherhoodsRelatedByStudentId() as $brother)
    {
      $brothers[] = StudentPeer::retrieveByPK($brother->getBrotherId());
    }
    return $brothers;

  }

  public function canBeDeleted()
  {
    return !$this->countCourseSubjectStudents() && !$this->countStudentApprovedCareerSubjects() && !$this->countStudentApprovedCourseSubjects() && !$this->countDivisionStudents() && !$this->getStudentAttendancesPerDay();

  }

  public function getMessageCantBeDeleted()
  {
    return "The student can not be removed because: id enrolled in a course, already has passed a subject, already has passed a course, or has some related data stored.";
  }

  public function isInscriptedInCareer()
  {
    return $this->countCareerStudents();

  }

  /**
   * This method returns the current StudentCareerSchoolYear. The student has to be inscripted and matriculated in a Career.
   *
   * @return StudentCareerSchoolYear
   */
  public function getCurrentStudentCareerSchoolYear()
  {
    $c = new Criteria();

    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    $c->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->add(StudentCareerSchoolYearPeer::STUDENT_ID, $this->getId());

    $object = StudentCareerSchoolYearPeer::doSelectOne($c);

    return $object;

  }

  public function getCurrentStudentCareerSchoolYears()
  {
    $c = new Criteria();

    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    $c->addJoin(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->add(StudentCareerSchoolYearPeer::STUDENT_ID, $this->getId());

    return StudentCareerSchoolYearPeer::doSelect($c);

  }

  public function getCurrentCourseYear()
  {
    return $this->getCurrentStudentCareerSchoolYear() ? $this->getCurrentStudentCareerSchoolYear()->getYear() : 'Egresado';

  }

  public function getStudentAttendancesPerDay(Criteria $c = null, PropelPDO $con = null)
  {
    $c = is_null($c) ? new Criteria() : $c;
    $c->add(StudentAttendancePeer::STUDENT_ID, $this->id);
    $c->add(StudentAttendancePeer::COURSE_SUBJECT_ID, null, Criteria::ISNULL);
    return parent::getStudentAttendances($c, $con);

  }

  public function getStudentAttendancesAbsencePerDay(Criteria $c = null, PropelPDO $con = null)
  {
    $c = is_null($c) ? new Criteria() : $c;
    $c->add(StudentAttendancePeer::VALUE, 0, Criteria::GREATER_THAN);
    $c->add(StudentAttendancePeer::STUDENT_ID, $this->getId());
    $c->add(StudentAttendancePeer::COURSE_SUBJECT_ID, null, Criteria::ISNULL);
    return parent::getStudentAttendances($c, $con);

  }

  public function getStudentAttendancesAbsencePerSubject($career_school_year, PropelPDO $con = null)
  {
    $c = new Criteria();
    $c->add(StudentAttendancePeer::STUDENT_ID, $this->id);
    $c->add(StudentAttendancePeer::VALUE, 0, Criteria::GREATER_THAN);
    $c->add(StudentAttendancePeer::COURSE_SUBJECT_ID, null, Criteria::ISNOTNULL);

    $c->add(StudentAttendancePeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());

    return parent::getStudentAttendances($c, $con);

  }

  public function hasAttendancesPerDay()
  {
    return 0 != count($this->getStudentAttendancesAbsencePerDay());

  }

  public function hasAttendancesPerSubject()
  {
    $c = new Criteria();
    $c->add(StudentAttendancePeer::STUDENT_ID, $this->id);
    $c->add(StudentAttendancePeer::VALUE, 0, Criteria::GREATER_THAN);

    $c->add(StudentAttendancePeer::COURSE_SUBJECT_ID, null, Criteria::ISNOTNULL);
    return 0 != count(parent::getStudentAttendances($c));

  }

  public function getConductPeriod(CareerSchoolYearPeriod $period)
  {
    $scsy = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($this, $period->getCareerSchoolYear());
    return StudentCareerSchoolYearConductPeer::retriveByStudentCareerSchoolYearAndPeriod($scsy, $period);

  }

  public function getStudentReincorporationsPerDay()
  {
    $per_day = array();
    foreach ($this->getStudentReincorporations() as $reincorporation)
    {
      if (!$reincorporation->hasSubject())
      {
        $per_day[] = $reincorporation;
      }
    }

    return $per_day;

  }

  public function getStudentReincorporationsPerSubject()
  {
    $per_subject = array();
    foreach ($this->getStudentReincorporations() as $reincorporation)
    {
      if ($reincorporation->hasSubject())
      {
        $per_subject[] = $reincorporation;
      }
    }

    return $per_subject;

  }

  public function getMarksForCourse(CourseSubject $course_subject)
  {

    $css = CourseSubjectStudentPeer::retrieveByCourseSubjectAndStudent($course_subject->getId(), $this->getId());
    if (!is_null($css))
    {
      $cssm = CourseSubjectStudentMarkPeer::retrieveByCourseSubjectStudent($css->getId());
      return $cssm;
    }
    return NULL;

  }

  /*
   * This function gets 1°quaterly, and then returns those course subject student whose
   * course type is bimester and that are due to be coursed during 1°quaterly.
   *
   * @return Array
   */

  public function getCourseSubjectStudentsForBimesterFirstQuaterly($student_career_school_year = null)
  {
    if (is_null($student_career_school_year))
    {
      $career_school_years = StudentCareerSchoolYearPeer::retrieveCareerSchoolYearForStudentAndYear($this, SchoolYearPeer::retrieveCurrent());
      $student_career_school_year = array_shift($career_school_years);
    }

    $career_school_year = $student_career_school_year->getCareerSchoolYear();
    $first_quaterly = CareerSchoolYearPeriodPeer::retrieveFirstQuaterlyForCareerSchoolYear($career_school_year);

    return $this->getCourseSubjectStudentsForBimesterQuaterly($first_quaterly, $student_career_school_year);

  }

  /*
   * This function gets 2°quaterly, and then returns those course subject student whose
   * course type is bimester and that are due to be coursed during 2°quaterly.
   *
   * @return Array
   */

  public function getCourseSubjectStudentsForBimesterSecondQuaterly($student_career_school_year = null)
  {
    if (is_null($student_career_school_year))
    {
      $career_school_years = StudentCareerSchoolYearPeer::retrieveCareerSchoolYearForStudentAndYear($this, SchoolYearPeer::retrieveCurrent());
      $student_career_school_year = array_shift($career_school_years);
    }

    $career_school_year = $student_career_school_year->getCareerSchoolYear();

    $second_quaterly = CareerSchoolYearPeriodPeer::retrieveSecondQuaterlyForCareerSchoolYear($career_school_year);

    return $this->getCourseSubjectStudentsForBimesterQuaterly($second_quaterly, $student_career_school_year);

  }

  /*
   * Returns those bimestral course subject student that are due to be coursed during the quaterly given as parameter.
   *
   * @parame CareerSchoolYearPeriod $quaterly
   * @return Array
   */

  public function getCourseSubjectStudentsForBimesterQuaterly($quaterly, $student_career_school_year = null)
  {
    $results = array();

    foreach ($this->getCourseSubjectStudentsForCourseType(CourseType::BIMESTER, $student_career_school_year) as $css)
    {
      $subject_configurations = CourseSubjectConfigurationPeer::retrieveBySubject($css->getCourseSubject());
      foreach ($subject_configurations as $sc)
      {
        if ($sc->getCareerSchoolYearPeriod()->getCareerSchoolYearPeriodId() == $quaterly->getId())
          $results[$css->getId()] = $css;
      }
    }
    return $results;

  }

  public function getCourseSubjectStudentsForCourseType($course_type, $student_career_school_year = null)
  {
    $school_year = is_null($student_career_school_year) ? null : $student_career_school_year->getCareerSchoolYear()->getSchoolYear();

    return SchoolBehaviourFactory::getInstance()->getCourseSubjectStudentsForCourseType($this, $course_type, $school_year);
  }

  public function getCourseSubjectStudentsForCourseTypeAndAttendanceForSubject($course_type, $student_career_school_year = null)
  {
    $school_year = is_null($student_career_school_year) ? null : $student_career_school_year->getCareerSchoolYear()->getSchoolYear();

    return SchoolBehaviourFactory::getInstance()->getCourseSubjectStudentsForCourseTypeAndAttendanceForSubject($this, $course_type, $school_year);

  }

  public function getCourseSubjectStudentsForCourseTypeAndAttendanceForDay($course_type, $student_career_school_year = null)
  {
    $school_year = is_null($student_career_school_year) ? null : $student_career_school_year->getCareerSchoolYear()->getSchoolYear();

    return SchoolBehaviourFactory::getInstance()->getCourseSubjectStudentsForCourseTypeAndAttendanceForDay($this, $course_type, $school_year);

  }

  /**
   * If the student is inscripted in the current school year.
   *
   * @return boolean
   */
  public function canBeFree()
  {
    return count($this->getCurrentStudentCareerSchoolYear()) > 0;

  }

  public function getMessageCantBeFree()
  {
    return "The student can not be free because, is not enrrolled in the current school year.";

  }

  /**
   * This method returns true if has been free at least one time in the current year and if flag is_free is true.
   * @return boolean
   */
  public function canBeReincorporated()
  {
    return count(StudentFreePeer::retrieveCurrentAndIsFree(null, $this->getId())) > 0;

  }

  public function getMessageCantBeReincorporated()
  {
    return "The student can not be reincorporated because, inst free in any period.";

  }

  public function isFreeInDay($day, $course_subject)
  {
    if (is_null($course_subject_id))
    {
      $career_school_year_period_type = SchoolBehaviourFactory::getInstance()->getDivisionCourseType();
    }
    else
    {
      $career_school_year_period_type = $course_subject->getCourseType();
    }

    $career_school_year_period = CareerSchoolYearPeriodPeer::retrieveByDateAndCourseType($day, $career_school_year_period_type);

    return $this->isFree($career_school_year_period, $course_subject);

  }

  public function isFree($career_school_year_period = null, $course_subject = null, $career_school_year)
  {
    return SchoolBehaviourFactory::getInstance()->isFreeStudent($this, $career_school_year_period, $course_subject, $career_school_year);

  }

  public function getFinalMarkForCareerSubject($career_subject_school_year)
  {

    if ($career_subject_school_year instanceof CareerSubjectSchoolYear)
    {
      $c = new Criteria();
      $c->add(StudentApprovedCareerSubjectPeer::STUDENT_ID, $this->getId());
      $c->add(StudentApprovedCareerSubjectPeer::CAREER_SUBJECT_ID, $career_subject_school_year->getCareerSubjectId());
      return StudentApprovedCareerSubjectPeer::doSelectOne($c);
    }
    elseif ($career_subject_school_year instanceof CareerSubject)
    {
      $c = new Criteria();
      $c->add(StudentApprovedCareerSubjectPeer::STUDENT_ID, $this->getId());
      $c->add(StudentApprovedCareerSubjectPeer::CAREER_SUBJECT_ID, $career_subject_school_year->getId());
      return StudentApprovedCareerSubjectPeer::doSelectOne($c);
    }
    else
    {
      die('Student getFinalMarkForCareerSubject not instanceOf CareerSubjectSchoolYear or CareerSubject ');
    }

  }

  public function getErrorsWithCourseSubjectsStudent($career_school_year)
  {
    $c = new Criteria();

    $c->add(CareerSchoolYearPeer::ID, $career_school_year->getId());
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->addJoin(CareerSubjectSchoolYearPeer::ID, CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $this->getId());
    $c->clearSelectColumns();
    $c->addSelectColumn(CareerSubjectSchoolYearPeer::ID);

    $stmt = CareerSubjectSchoolYearPeer::doSelectStmt($c);
    //me quedo solo con los IDs de los CareerSubjectSchoolYear
    $array = $stmt->fetchAll(PDO::FETCH_COLUMN);
    unset($stmt);
    //ordeno de mayot a menor
    arsort($array);
    //armo un arreglo con las claves de los CareerSubjectSchoolYear->ID y
    //valor la cantidad de veces que esta adentro del arreglo
    $array_count = array_count_values($array);
    unset($array);
    //Filtro los valores que son menores a 1
    $array_filtered = array_filter($array_count, create_function('$each', 'return $each>1;'));
    CareerSubjectSchoolYearPeer::clearInstancePool();
    unset($array_count);

    if (!empty($array_filtered))
    {
      $array_filtered = SchoolBehaviourFactory::getEvaluatorInstance()->evaluateErrorsWithCareerSubjectSchoolYear($array_filtered);
    }

    return $array_filtered;

  }

  public function deleteAllCareerSubjectAlloweds(PropelPDO $con)
  {
    $c = new Criteria();
    $c->add(StudentCareerSubjectAllowedPeer::STUDENT_ID, $this->getId());

    StudentCareerSubjectAllowedPeer::doDelete($c, $con);

    StudentCareerSubjectAllowedPeer::clearInstancePool();
    unset($c);

  }

  public function getShiftForSchoolYear($school_year)
  {
    $c = new Criteria();
    $c->add(SchoolYearStudentPeer::STUDENT_ID, $this->getId());
    $c->add(SchoolYearStudentPeer::SCHOOL_YEAR_ID, $school_year->getId());

    $school_year_student = SchoolYearStudentPeer::doSelectOne($c);

    SchoolYearStudentPeer::clearInstancePool();

    return is_null($school_year_student) ? null : $school_year_student->getShift();

  }

  /**
   * Returns true if student's disciplinary sanctions are as many as the maximun defined at subject_configuration table.
   * @return boolean
   */
  public function hasManyDisciplinarySanctions()
  {
    $subject_configuration = $this->getCurrentStudentCareerSchoolYear()->getCareerSchoolYear()->getSubjectConfiguration();

    if ($subject_configuration->getMaxDisciplinarySanctions() == 0)
      return false;

    return ($subject_configuration->getMaxDisciplinarySanctions() <= $this->countStudentDisciplinarySanctionsForSchoolYear());

  }

  /**
   * This method check if the student is currently coursing some course of the $course_type
   *
   */
  public function hasCourseType($course_type, $student_career_school_year = null)
  {
    return count($this->getCourseSubjectStudentsForCourseType($course_type, $student_career_school_year)) > 0;

  }

  public function canGenerateReport()
  {
    return count($this->getCurrentDivisions()) != 0;

  }

  public function canPrintReportCard()
  {
    return $this->countDivisionStudents() > 0;

  }

  public function getStudentDisapprovedCourseSubjectString()
  {
    $results = $this->getStudentDisapprovedCourseSubject();
    $text = '';

	  if ($results){
      /* @var $result StudentDisapprovedCourseSubject */
      foreach ($results as $result)
      {
        $text .=$result->getCourseSubject();
        $text .=' - ';
      }
      $text = trim($text, ' - ');
  }
    return $text;

  }

	public function getStudentDisapprovedCourseSubject()
	{
		$last_year_school_year = SchoolYearPeer::retrieveLastYearSchoolYear(SchoolYearPeer::retrieveCurrent());
		if (!is_null($last_year_school_year)) {
			$criteria = new Criteria();

			$criteria->addJoin(CourseSubjectStudentPeer::STUDENT_ID, $this->getId());
			$criteria->addJoin(StudentDisapprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);

			$criteria->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
			$criteria->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
			$criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);

			$criteria->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $last_year_school_year->getId());

			return StudentDisapprovedCourseSubjectPeer::doSelect($criteria);
		}
	}

  public function getAmountStudentAttendanceUntilDay($day, $school_year = null)
  {
    if (is_null($school_year))
    {
      $school_year = SchoolYearPeer::retrieveCurrent();
    }

    $c = new Criteria();
    $c->add(StudentAttendancePeer::DAY, $day, Criteria::LESS_EQUAL);
    $c->addJoin(StudentAttendancePeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    $c->addJoin(CareerSchoolYearPeer::SCHOOL_YEAR_ID, SchoolYearPeer::ID);
    $c->add(SchoolYearPeer::ID, $school_year->getId());
    $cant = $this->getStudentAttendancesAbsencePerDay($c);

    $total = 0;
    foreach ($cant as $c)
    {
      $total += $c->getValue();
    }
    return $total;

  }

  public function countStudentDisciplinarySanctionsForSchoolYear($school_year = null)
  {
    return StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctionsForPeriod($this, $school_year);

  }

  public function getStudentDisciplinarySanctionsForSchoolYear($school_year)
  {
    $criteria = new Criteria();
    $criteria->add(StudentDisciplinarySanctionPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $criteria->add(StudentDisciplinarySanctionPeer::STUDENT_ID, $this->getId());

    return $this->getStudentDisciplinarySanctions($criteria);

  }

  /*
   * This method retrieves the examinations repproved for this student in the $school_year
   *
   * @param $school_year SchoolYear
   * @return array StudentRepprovedCourseSubject
   */

  public function getStudentRepprovedCourseSubjectForSchoolYear(SchoolYear $school_year = null)
  {
    if (is_null($school_year))
    {
      return array();
    }

    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $this->getId());
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    $c->add(CoursePeer::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addJoin(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);

    return StudentRepprovedCourseSubjectPeer::doSelect($c);
  }

  public function checkIfRepprovedAreNotApproved($examination_repproveds)
  {
    foreach ($examination_repproveds as $examination_repproved)
    {
      if (is_null($examination_repproved->getStudentApprovedCareerSubject()))
      {
        return true;
      }
    }
    return false;

  }

  public function getCareerStudent()
  {
    $c = new Criteria();
    $c->add(CareerStudentPeer::STUDENT_ID, $this->getId());
    #$c->add(CareerStudentPeer::FILE_NUMBER,$this->getGlobalFileNumber());

    return CareerStudentPeer::doSelectOne($c);
  }

  public function getCareerStudents($criteria = null, PropelPDO $con = null)
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(CareerStudentPeer::CREATED_AT);

    return parent::getCareerStudents($c);
  }

  public function getLastCareerStudent()
  {
    $c = new Criteria();
    $c->add(CareerStudentPeer::STUDENT_ID, $this->getId());
    $c->addJoin(CareerPeer::ID, CareerStudentPeer::CAREER_ID);
    $c->addDescendingOrderByColumn(CareerStudentPeer::CREATED_AT);

    return CareerStudentPeer::doSelectOne($c);
  }

  public function getLastStudentCareerSchoolYear()
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::YEAR);
    $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::CREATED_AT);
    $c->add(StudentCareerSchoolYearPeer::STUDENT_ID, $this->getId());

    return StudentCareerSchoolYearPeer::doSelectOne($c);
  }

  public function getCommisions($school_year = null)
  {
    is_null($school_year) ? $school_year = SchoolYearPeer::retrieveCurrent() : $school_year;

    return CoursePeer::retrieveComissionsForSchoolYearAndStudent($school_year, $this);
  }

  public function getPromDef($course_result)
  {

    if (is_null($course_result))
    {
      return '';
    }
    if ($course_result instanceOf StudentApprovedCourseSubject)
    {

      if ($course_result->getCareerSchoolYear()->getSubjectConfiguration()->getNecessaryStudentApprovedCareerSubjectToShowPromDef())
      {
        return ($student_approved_career_subject = $course_result->getStudentApprovedCareerSubject()) ? number_format($student_approved_career_subject->getMark(), 2, '.', '') : '&nbsp';
      }
      else
      {
        return number_format($course_result->getMark(), 2, '.', '');
      }
    }
    else
    {
      return ($course_result->getStudentApprovedCareerSubject()) ? number_format($course_result->getStudentApprovedCareerSubject()->getMark(), 2, '.', '') : '&nbsp';
    }
  }

  public function canBeDeactivated()
  {
    $c = new Criteria();
    $c->add(SchoolYearStudentPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
    $c->add(SchoolYearStudentPeer::STUDENT_ID, $this->getId());

    return (count(SchoolYearStudentPeer::doSelect($c)) == 0) && ($this->getPerson()->getIsActive());
  }

  public function getCareerFromStudentStatsFilters()
  {
    $user = sfContext::getInstance()->getUser();
    $filters = $user->getAttribute('student_stats.filters', null, 'admin_module');

    if (!isset($filters['career_school_year']) || is_null($filters['career_school_year']))
    {
      $school_year = SchoolYearPeer::retrieveByPK($filters['school_year']);
      $result = implode(', ', CareerSchoolYearPeer::retrieveBySchoolYear(null, $school_year));
    }
    else
    {
      $result = CareerSchoolYearPeer::retrieveByPK($filters['career_school_year']);
    }

    return $result;
  }

  public function getDivisionFromStudentStatsFilters()
  {
    $user = sfContext::getInstance()->getUser();
    $filters = $user->getAttribute('student_stats.filters', null, 'admin_module');

    if (!isset($filters['division']) || is_null($filters['division']))
    {
      if (!isset($filters['career_school_year']) || is_null($filters['career_school_year']))
        $result = implode(', ', DivisionPeer::retrieveStudentSchoolYearDivisions(SchoolYearPeer::retrieveByPK($filters['school_year']), $this));
      else
      {
        $career_school_year_id = $filters['career_school_year'];
        $result = $this->getCurrentDivisionsString($career_school_year_id);
      }
    }
    else
      $result = DivisionPeer::retrieveByPK($filters['division']);

    return $result;
  }

  public function getShiftFromStudentStatsFilters()
  {
    $user = sfContext::getInstance()->getUser();
    $filters = $user->getAttribute('student_stats.filters', null, 'admin_module');

    if (!isset($filters['shift']) || is_null($filters['shift']))
    {
      if (!isset($filters['division']) || is_null($filters['division']))
      {
        $result = array();
        if (!isset($filters['career_school_year']) || is_null($filters['career_school_year']))
          foreach (DivisionPeer::retrieveStudentSchoolYearDivisions(SchoolYearPeer::retrieveByPK($filters['school_year']), $this) as $division)
            $result[$division->getShift()->getName()] = $division->getShift();
        else
        {
          $career_school_year_id = $filters['career_school_year'];
          $divisions = $this->getCurrentDivisions($career_school_year_id);
          foreach ($divisions as $division)
            $result[$division->getShift()->getName()] = $division->getShift()->getName();
        }

        return implode(', ', $result);
      }
      else
      {
        return DivisionPeer::retrieveByPK($filters['division'])->getShift();
      }
    }
    else
      return ShiftPeer::retrieveByPK($filters['shift']);
  }

  public function getCourseSubjectStudentsForSchoolYear($school_year)
  {
    $c = new Criteria();
    $c->add(CoursePeer::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addJoin(CourseSubjectPeer::COURSE_ID, CoursePeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->addAscendingOrderByColumn(CareerSubjectSchoolYearPeer::INDEX_SORT);

    return $this->getCourseSubjectStudents($c);
  }

  public function getStudentSpecialityString()
  {
    return ($this->getCareerStudent() != null) ? $this->getCareerStudent()->getSubOrientation() : "";
  }

  public function getStudentOrientationString()
  {
    return ($this->getCareerStudent() != null) ? $this->getCareerStudent()->getOrientation() : "";
  }

  public function canBeWithdrawn()
  {
    return (is_null($this->getSchoolYearStudentForSchoolYear())) && !is_null($this->getCurrentOrLastStudentCareerSchoolYear()) && $this->getCurrentOrLastStudentCareerSchoolYear()->getStatus() != StudentCareerSchoolYearStatus::WITHDRAWN;
  }

  public function canUndoWithdrawn()
  {
    return !is_null($this->getCurrentOrLastStudentCareerSchoolYear()) && $this->getCurrentOrLastStudentCareerSchoolYear()->getStatus() == StudentCareerSchoolYearStatus::WITHDRAWN;
  }

   public function getMessageCantBeWithdrawn()
  {
    return "The student cant be withdrawn because it is still matriculated.";
  }

   public function getMessageCantUndoWithdrawn()
  {
    return "Undo withdraw can be done only if student is withdrawn.";
  }

   public function getPersonIsActiveString()
  {
    return $this->getPerson()->getIsActiveString();
  }

   public function getIsRegisteredString()
  {
    return ($this->getIsRegistered())? 'Sí': 'No';
  }

  /**
   * This method returns the current or last StudentCareerSchoolYear.
   *
   * @return StudentCareerSchoolYear
   */
  public function getCurrentOrLastStudentCareerSchoolYear()
  {
    $object = $this->getCurrentStudentCareerSchoolYear();
    if (is_null($object))
    {
      return $this->getLastStudentCareerSchoolYear();
    }
    else
      return $object;
  }

 public function getStudentCareerSchoolYearsAscending()
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::YEAR);

    return $this->getStudentCareerSchoolYears($c);
  }

 public function getCareerSchoolYearsNames()
  {
    return implode(', ', array_map(create_function('$scsy', 'return $scsy->getCareerSchoolYear();'), $this->getStudentCareerSchoolYearsAscending()));
  }

 public function getStudentRepprovedCourseSubjectForRepordCards($school_year)
  {
    $school_years = SchoolYearPeer::retrieveLastYearSchoolYears($school_year);
    $repproveds = array();
    foreach ($school_years as $sy)
    {
      $srcs = $this->getStudentRepprovedCourseSubjectForSchoolYear($sy);
      $repproveds = array_merge($repproveds, $srcs);
    }

    $repproveds_to_show = array();

    foreach ($repproveds as $r)
    {
      //si está pendiente
      if (is_null($r->getStudentApprovedCareerSubject())) {
        $repproveds_to_show[] = $r;
      }
      //si fue aprobada en el año que se está renderizando
      elseif ($school_year->getYear() == ($r->getApprovalYear())){
        $repproveds_to_show[] = $r;
      }
    }

    return $repproveds_to_show;
  }


	/**
	 * Returns if the student is inscripted in pathway program for current school year
	 *
	 * @return boolean
	 */
	public function getBelongsToPathway()
	{
		$c = new Criteria();
		$c->add(PathwayStudentPeer::STUDENT_ID, $this->getId());
		//$c->addJoin(PathwayPeer::ID, PathwayStudentPeer::PATHWAY_ID, Criteria::INNER_JOIN);
		//$c->add(PathwayPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());

		return $this->countPathwayStudents($c) > 0;
	}

	public function owsCorrelativeFor($career_subject) {
    //obtengo las correlativas de la materia recibida por parámetro
    $correlatives = $career_subject->getCorrelativeCareerSubjects();

		$career_subjects_repproveds_array = array();

		foreach ($this->getStudentRepprovedCourseSubjectForRepordCards(SchoolYearPeer::retrieveCurrent()) as $repproved) {
			$career_subjects_repproveds_array[] = $repproved->getCourseSubjectStudent()->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubject();
		}

		return count(array_intersect($correlatives, $career_subjects_repproveds_array)) > 0;
	}

}

sfPropelBehavior::add('Student', array('person_delete'));