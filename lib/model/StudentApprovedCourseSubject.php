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

class StudentApprovedCourseSubject extends BaseStudentApprovedCourseSubject
{

  public function __toString()
  {
    return SchoolBehaviourFactory::getEvaluatorInstance()->getStudentApprovedResultString($this);

  }

  public function getClass()
  {
    return 'aprobado';
  }

  /*
   * This method creates de StudentApprovedCareerSubject
   */

  public function close(PropelPDO $con = null)
  {
    SchoolBehaviourFactory::getEvaluatorInstance()->closeCourseSubjectStudent($this, $con);
  }

  public function getCourseSubjectStudent()
  {
    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, $this->getCourseSubjectId());
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $this->getStudentId());
    return CourseSUbjectStudentPeer::doSelectOne($c);

  }

  public function getResultStr()
  {
    return sprintf("%01.2f", $this->getMark());

  }

  public function getResultWithMark()
  {
    return __("Approved %mark%", array("%mark%" => $this->getMark()));

  }

  public function renderChangeLog()
  {
    return ncChangelogRenderer::render($this, 'tooltip', array('credentials' => 'view_changelog'));

  }

  public function getCareerSubject()
  {
    return $this->getCourseSubject()->getCareerSubject();

  }

  public function getCareerSchoolYear()
  {
    return CareerSchoolYearPeer::retrieveByCareerAndSchoolYear($this->getCareerSubject()->getCareer(), $this->getSchoolYear());
  }

  public function isApproved()
  {
    return true;
  }

  public function getFinalMark()
  {
    return $this->getMark();
  }

  public function getColor()
  {
    return 'mark_green';
  }

}

sfPropelBehavior::add('StudentApprovedCourseSubject', array('student_approved_course_subject'));