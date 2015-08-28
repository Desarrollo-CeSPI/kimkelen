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

class CareerSubjectSchoolYear extends BaseCareerSubjectSchoolYear
{

  public function __toString()
  {
    return $this->getCareerSubject() . ' | ' . $this->getSchoolYear();
  }

   public function getFullToString()
   {
    return sprintf("%s | %s | %s", $this->getCareerSubject()->getCareer(), $this->getCareerSubject(),  $this->getSchoolYear());
   }

  public function getSchoolYear()
  {
    return $this->getCareerSchoolYear()->getSchoolYear();
  }

  public function canBeEdited(PropelPDO $con = null)
  {
    return $this->getCareerSchoolYear()->getSchoolYear()->getIsActive();
  }

  public function getSubjectConfigurationOrCreate()
  {
    $subject_configuration = $this->getSubjectConfiguration();

    if (is_null($subject_configuration))
    {
      $subject_configuration = new SubjectConfiguration();
      $this->getCareerSchoolYear()->getSubjectConfiguration()->copyInto($subject_configuration, false);
    }

    return $subject_configuration;
  }

  public function hasChoices()
  {
    return $this->countOptionalCareerSubjectsRelatedByCareerSubjectSchoolYearId();
  }

  public function getChoices()
  {
    return $this->getOptionalCareerSubjectsRelatedByCareerSubjectSchoolYearId();
  }

  public function getAvailableChoices()
  {
    return SchoolBehaviourFactory::getInstance()->getAvailableChoicesForCareerSubjectSchoolYear($this);
  }

  /*
   * Creates the course subjects for this. If has options then creates courseSubjects for the options.
   */

  public function createCourseSubject($course, PropelPDO $con = null)
  {
    if ($this->hasChoices())
    {
      foreach ($this->getChoices() as $choice)
      {
        $course_subject = new CourseSubject();
        $course_subject->setCourse($course);
        $course_subject->setCareerSubjectSchoolYear($choice->getCareerSubjectSchoolYearRelatedByChoiceCareerSubjectSchoolYearId());
        $course_subject->save($con);
      }
    }
    else
    {
      $course_subject = new CourseSubject();
      $course_subject->setCourse($course);
      $course_subject->setCareerSubjectSchoolYear($this);
      $course_subject->save($con);

      if ($course->getDivision())
      {
        foreach ($course->getDivision()->getDivisionStudents() as $ds)
        {
          $course_subject_student = new CourseSubjectStudent();
          $course_subject_student->setCourseSubject($course_subject);
          $course_subject_student->setStudent($ds->getStudent());
          $course_subject_student->save($con);
        }
      }
    }

  }

  /**
   * This method returns the parent of the option.
   */
  public function getOptionalCareerSubjectSchoolYear()
  {
    $c = new Criteria();
    $c->add(OptionalCareerSubjectPeer::CHOICE_CAREER_SUBJECT_SCHOOL_YEAR_ID, $this->getId());
    $c->addJoin(OptionalCareerSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);

    return CareerSubjectSchoolYearPeer::doSelectOne($c);

  }

  /**
   * This method returns the configuration. If the career_subject_school_year does not  have own configuration, then returns the career's configuration.
   * If the career_subject_school_year is a option, then returns the parent configuration if it has it. Else it returns career configuration
   * @return <type>
   */
  public function getConfiguration(propelPDO $con = null)
  {
    if ($this->getCareerSubject()->getIsOption())
    {
      return $this->getOptionalCareerSubjectSchoolYear()? $this->getOptionalCareerSubjectSchoolYear()->getConfiguration($con): $this->getCareerSchoolYear($con)->getSubjectConfiguration($con);
    }

    return $this->getSubjectConfiguration() ? $this->getSubjectConfiguration() : $this->getCareerSchoolYear($con)->getSubjectConfiguration($con);
  }

  public function isAttendanceForDay()
  {
    return $this->getConfiguration()?$this->getConfiguration()->getAttendanceType() == SchoolBehaviourFactory::getInstance()->getAttendanceDay():null;

  }

  public function getLastYearCareerSubjectSchoolYear()
  {
    $last_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($this->getSchoolYear());

    $last_year_career_subject_school_year = CareerSubjectSchoolYearPeer::retrieveByCareerSubjectAndSchoolYear($this->getCareerSubject(), $last_school_year);

    return $last_year_career_subject_school_year;

  }

  public function copyLastYearSort()
  {
    $last_year_career_subject_school_year = $this->getLastYearCareerSubjectSchoolYear();
    if (is_null($last_year_career_subject_school_year))
    {
      return 0;
    }

    $this->setIndexSort($last_year_career_subject_school_year->getIndexSort());

  }

  public function copyLastYearConfiguration()
  {
    $last_year_career_subject_school_year = $this->getLastYearCareerSubjectSchoolYear();

    if (!is_null($last_year_career_subject_school_year) && !is_null($last_year_career_subject_school_year->getSubjectConfigurationId()))
    {
      $subject_configuration = $last_year_career_subject_school_year->getSubjectConfiguration()->copy();
      $this->setSubjectConfiguration($subject_configuration);
    }

  }

  public function getCantApprovedCurrentSchoolYear()
  {

    $sum = 0;
    /* @var $course_subject CourseSubject */
    foreach ($this->getCourseSubjects() as $course_subject)
    {
      $sum+= count($course_subject->getStudentApprovedCourseSubjects());
    }

    return $sum;
  }

  public function getCantStudents()
  {

    $sum = 0;
    /* @var $course_subject CourseSubject */
    foreach ($this->getCourseSubjects() as $course_subject)
    {
      $sum+= count($course_subject->getCourseSubjectStudents());
    }
    return $sum;

  }

  public function getSubject()
  {

    $subjects = $this->getCourseSubjects();

    return array_shift($subjects);

  }

  public function getYear()
  {
    return $this->getCareerSubject()->getYear();
  }
}