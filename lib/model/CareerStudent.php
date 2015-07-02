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

class CareerStudent extends BaseCareerStudent
{

  /**
   * Returns the status string.
   *
   * @return string
   */
  public function getStatusString()
  {
    $css = CareerStudentStatus::getInstance("CareerStudentStatus");

    return $css->getStringFor($this->getStatus());

  }

  /**
   * Returns the string representation of this career student.
   * If school behaviour determines that filenumber is global, then
   * returns only career name, otherwise, returns both, file number and career name
   *
   * @return String
   */
  public function __toString()
  {
    return sprintf(
        "%05s - %s (%s): %s", $this->getFileNumber(), $this->getCareer(), $this->getSubOrientation() ? $this->getOrientation() . " - " . $this->getSubOrientation() : $this->getOrientation(), $this->getStatusString()
    );

  }

  /**
   * When this object can't be deleted
   * @see @student actions
   * @return boolean
   */
  public function canBeDeleted()
  {

    return (count($this->getStudentApprovedCareerSubjects())) == 0;

  }

  /**
   * String representation of the cantBeEdited cause
   *
   * @return string
   */
  public function getMessageCantBeDeleted()
  {
    return 'Career cant be deleted';

  }

  /**
   *
   * @param <int> $start_year
   */
  public function createStudentsCareerSubjectAlloweds($start_year = null, PropelPDO $con = null)
  {
    if (is_null($start_year))
    {
      $start_year = CareerSubjectPeer::FIRST_YEAR;
    }

    if ($con == null)
    {
      $con = Propel::getConnection(StudentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }


    try
    {
      $con->beginTransaction();
      $c = new Criteria();
      $c->add(CareerSubjectPeer::YEAR, $start_year);

      //This criterion checks if the careerSubject has orientation, if has an orientation then filters for studentCareer orientation.
      $criterion = $c->getNewCriterion(CareerSubjectPeer::ORIENTATION_ID, null, Criteria::ISNULL);
      $criterion->addOr($c->getNewCriterion(CareerSubjectPeer::ORIENTATION_ID, $this->getOrientationId()));
      $c->add($criterion);

      $career_subjects = $this->getCareer()->getCareerSubjects($c, $con);
      CareerSubjectPeer::clearInstancePool();
      unset($c);

      foreach ($career_subjects as $career_subject)
      {

        $student_career_subject_allowed = new StudentCareerSubjectAllowed();
        $student_career_subject_allowed->setStudent($this->getStudent($con));
        $student_career_subject_allowed->setCareerSubject($career_subject);
        $student_career_subject_allowed->save($con);

        unset($student_career_subject_allowed);
      }
      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

  public function deleteStudentsCareerSubjectAlloweds()
  {
    $con = Propel::getConnection();

    try
    {
      $con->beginTransaction();
      foreach ($this->getStudent()->getStudentCareerSubjectAlloweds() as $allowed)
      {
        $allowed->delete($con);
      }

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

  public function createStudentCareerSchoolYear(PropelPDO $con)
  {
    $c = new Criteria();
    $c->addJoin(SchoolYearPeer::ID, SchoolYearStudentPeer::SCHOOL_YEAR_ID);
    $c->add(SchoolYearPeer::IS_ACTIVE, true);
    $c->add(SchoolYearStudentPeer::STUDENT_ID, $this->getStudentId());

    $school_year_student = SchoolYearStudentPeer::doSelectOne($c, $con);

    if ($school_year_student)
    {
      $career_school_year = $this->getCareer()->getCareerSchoolYear($school_year_student->getSchoolYear($con));

      if ($career_school_year &&
        (StudentCareerSchoolYearPeer::countByCareerAndStudent($this->getCareerId(), $this->getStudentId(), $school_year_student->getSchoolYearId(), $con) == 0))
      {
        $student_career_school_year = new StudentCareerSchoolYear();
        $student_career_school_year->setCareerSchoolYearId($career_school_year->getId());
        $student_career_school_year->setStudentId($this->getStudentId());
        $student_career_school_year->setYear($this->suggestYear());

        $student_career_school_year->save($con);
      }
    }

  }

  public function getCurrentStudentCareerSchoolYear(PropelPDO $con = null)
  {
    $c = new Criteria();
    $c->add(CareerSchoolYearPeer::CAREER_ID, $this->getCareerId());
    $c->addJoin(CareerSchoolYearPeer::ID, StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID);
    $c->add(StudentCareerSchoolYearPeer::STUDENT_ID, $this->getStudentId());
    $c->addDescendingOrderByColumn(StudentCareerSchoolYearPeer::YEAR);

    return StudentCareerSchoolYearPeer::doSelectOne($c, $con);

  }

  /**
   * This method suggest a year, if a StudentCareerSchoolYear does not exist, return the value
   * setted by the form, if this value does not exists, return 1.
   * If a StudentCareerSchoolYear exists, returns the year according to that StudentCareerSchoolYear.
   *
   * @return integer $year
   */
  public function suggestYear()
  {
    $current_student_career_school_year = $this->getCurrentStudentCareerSchoolYear();

    return is_null($current_student_career_school_year) ? $this->getStartYear() : $current_student_career_school_year->suggestYear();

  }

  public function graduate(PropelPDO $con = null)
  {
    $this->setStatus(CareerStudentStatus::GRADUATE);
    $this->save($con);

  }

  public function canGraduate(PropelPDO $con = null)
  {
    $c = new Criteria();
    $criterion = $c->getNewCriterion(CareerSubjectPeer::ORIENTATION_ID, $this->getOrientationId());
    $criterion->addOr($c->getNewCriterion(CareerSubjectPeer::ORIENTATION_ID, null, Criteria::ISNULL));

    $c->add($criterion);
    foreach ($this->getCareer()->getCareerSubjects($c, $con) as $career_subject)
    {
      if (!$this->getStudent()->hasApprovedCareerSubject($career_subject, $con))
      {
        return false;
      }
    }

    return true;

  }

  public function isRegular()
  {
    return $this->getStatus() == CareerStudentStatus::REGULAR;

  }

  public function isGraduate()
  {
    return $this->getStatus() == CareerStudentStatus::GRADUATE;

  }

  /**
   * Este metodo devuelve todas la materias que el alumno rindio. O promociono o tiene como equivalencia.
   *
   * @return <type>
   */
  public function getStudentApprovedCareerSubjects()
  {
    //Aprobadas
    $c = new Criteria();
    $c->add(StudentApprovedCareerSubjectPeer::STUDENT_ID, $this->getStudentId());
    $c->addJoin(StudentApprovedCareerSubjectPeer::CAREER_SUBJECT_ID, CareerSubjectPeer::ID, Criteria::INNER_JOIN);
    $c->addJoin(CareerSubjectPeer::CAREER_ID, CareerPeer::ID, Criteria::INNER_JOIN);

    CareerSubjectPeer::sorted($c);
    
    $c->add(CareerPeer::ID, $this->getCareerId());
    $c->addAscendingOrderByColumn(StudentApprovedCareerSubjectPeer::CREATED_AT);

    return StudentApprovedCareerSubjectPeer::doSelect($c);

  }

  public function deleteDivisionStudent()
  {

    $con = Propel::getConnection();

    try
    {
      $con->beginTransaction();
      $criteria = new Criteria();
      $criteria->add(CareerPeer::ID, $this->getCareerId());
      $criteria->addJoin(CareerPeer::ID, CareerSchoolYearPeer::CAREER_ID);
      $criteria->addJoin(CareerSchoolYearPeer::ID, DivisionPeer::CAREER_SCHOOL_YEAR_ID);
      $criteria->addJoin(DivisionPeer::ID, DivisionStudentPeer::DIVISION_ID);

      foreach ($this->getStudent()->getDivisionStudents($criteria) as $division_student)
      {
        $division_student->delete($con);
      }

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

  public function deleteStudentCareerSchoolYear()
  {

    $con = Propel::getConnection();

    try
    {
      $con->beginTransaction();
      $criteria = new Criteria();
      $criteria->add(CareerPeer::ID, $this->getCareerId());
      $criteria->addJoin(CareerPeer::ID, CareerSchoolYearPeer::CAREER_ID);
      $criteria->addJoin(CareerSchoolYearPeer::ID, StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID);
      $criteria->add(StudentCareerSchoolYearPeer::STUDENT_ID, $this->getStudentId());

      foreach (StudentCareerSchoolYearPeer::doSelect($criteria) as $student_career_school_year)
      {
        $student_career_school_year->delete($con);
      }

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

  public function deleteCourseSubjectStudent()
  {
    $con = Propel::getConnection();

    try
    {
      $con->beginTransaction();
      $criteria = new Criteria();
      $criteria->add(CareerPeer::ID, $this->getCareerId());
      $criteria->addJoin(CareerPeer::ID, CareerSchoolYearPeer::CAREER_ID);
      $criteria->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID,CareerSchoolYearPeer::ID);
      $criteria->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID,  CareerSubjectSchoolYearPeer::ID);
      $criteria->addJoin(CourseSubjectPeer::ID, CourseSubjectStudentPeer::COURSE_SUBJECT_ID);
      $criteria->add(CourseSubjectStudentPeer::STUDENT_ID,$this->getStudentId());



      foreach (CourseSubjectStudentPeer::doSelect($criteria) as $course_subject_student)
      {

        $course_subject_student->delete($con);

      }

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }

  }

}
sfPropelBehavior::add('CareerStudent', array('studentCareerSchoolYear'));