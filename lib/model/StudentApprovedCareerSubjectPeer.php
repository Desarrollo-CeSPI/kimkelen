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

class StudentApprovedCareerSubjectPeer extends BaseStudentApprovedCareerSubjectPeer
{

  public static function retrieveByCourseSubjectStudent(CourseSubjectStudent $course_subject_student, $school_year = null)
  {
    $c = new Criteria();
    $c->add(self::CAREER_SUBJECT_ID, $course_subject_student->getCourseSubject()->getCareerSubject()->getId());
    $c->add(self::STUDENT_ID, $course_subject_student->getStudentId());

    if ($school_year)
    {
      $c->add(self::SCHOOL_YEAR_ID, $school_year->getId());
    }

    return self::doSelectOne($c);

  }

  public static function retrieveOrCreateByCareerSubjectAndStudent($career_subject_id, $student_id)
  {
    $c = new Criteria();
    $c->add(self::CAREER_SUBJECT_ID, $career_subject_id);
    $c->add(self::STUDENT_ID, $student_id);
    $sacs = self::doSelectOne($c);

    if ($sacs == null)
    {
      $criteria = new criteria();
      $criteria->add(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID, $career_subject_id);
      $career_subject_school_years = CareerSubjectSchoolYearPeer::doSelect($criteria);
      $opcions = array();
      foreach ($career_subject_school_years as $career_subject_school_year)
      {
        foreach ($career_subject_school_year->getChoices() as $optional_career_subject_school_year)
        {
          $cs = CareerSubjectPeer::retrieveByCareerSubjectSchoolYearId($optional_career_subject_school_year->getChoiceCareerSubjectSchoolYearId());
          $opcions[] = $cs->getId();
        }
      }
      $criteria = new criteria();
      $criteria->add(self::CAREER_SUBJECT_ID, $opcions, Criteria::IN);
      $sacs = self::doSelectOne($criteria);
      if ($sacs == null)
      {
        $sacs = new StudentApprovedCareerSubject();
        $sacs->setCareerSubjectId($career_subject_id);
      }
    }
    $sacs->setStudentId($student_id);
    return $sacs;

  }

  static public function retrieveCriteriaForStudentCareerSchoolYear($student_career_school_year)
  {
    $c = new Criteria();
    $c->add(StudentApprovedCareerSubjectPeer::STUDENT_ID, $student_career_school_year->getStudentId());
    $c->addJoin(StudentApprovedCareerSubjectPeer::CAREER_SUBJECT_ID,  CareerSubjectPeer::ID);
    $c->add(CareerSubjectPeer::YEAR,$student_career_school_year->getYear());
    $c->add(StudentApprovedCareerSubjectPeer::SCHOOL_YEAR_ID, $student_career_school_year->getCareerSchoolYear()->getSchoolYearId());

    return $c;
  }

  static public function retrieveApprovationDate(StudentApprovedCareerSubject $studentApprovedCareerSubject)
  {
    $approvationInstance = StudentApprovedCourseSubjectPeer::retrieveByStudentApprovedCareerSubject($studentApprovedCareerSubject);
    if(!is_null($approvationInstance))
    {
      $period = $approvationInstance->getCourseSubject()->getLastCareerSchoolYearPeriod();

      if(!is_null($period))
      {
        return $period->getEndAt();
      }
    }

    $approvationInstance = StudentDisapprovedCourseSubjectPeer::retrieveByStudentApprovedCareerSubject($studentApprovedCareerSubject);

    if(!is_null($approvationInstance))
    {
      $cssid = $approvationInstance->getCourseSubjectStudentId();
      $csse = CourseSubjectStudentExaminationPeer::retrieveLastByCourseSubjectStudentId($cssid);
      $exam = $csse->getExaminationSubject()->getExamination();

      return $exam->getDateFrom();
    }

    $approvationInstance = StudentRepprovedCourseSubjectPeer::retrieveByStudentApprovedCareerSubject($studentApprovedCareerSubject);
    if(!is_null($approvationInstance))
    {
      $sers = StudentExaminationRepprovedSubjectPeer::retrieveByStudentRepprovedCourseSubject($approvationInstance);
      $exam = $sers->getExaminationRepprovedSubject()->getExaminationRepproved();

      return $exam->getDateFrom();
    }

    //couldn't find when was approved. return null ¿error?
    return;
  }
}

sfPropelBehavior::add('StudentApprovedCareerSubjectPeer', array('changelog'));