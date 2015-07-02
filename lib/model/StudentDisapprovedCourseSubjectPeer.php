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

class StudentDisapprovedCourseSubjectPeer extends BaseStudentDisapprovedCourseSubjectPeer
{
  public static function retrieveByStudentApprovedCourseSubject(StudentApprovedCourseSubject $approved_result, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;
    
    $c = new Criteria();
    $c->addJoin(CourseSubjectStudentPeer::ID, StudentDisapprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID);
    $c->add(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, $approved_result->getCourseSubjectId());
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $approved_result->getStudentId());
    $c->add(StudentDisapprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID, null, Criteria::ISNULL);
    
    return self::doSelectOne($c, $con);
  }

  public static function retrieveByStudentApprovedCareerSubject($studentApprovedCareerSubject, $criteria = null)
  {
    if(is_null($criteria))
    {
      $criteria = new Criteria();
    }

    $criteria->add(self::STUDENT_APPROVED_CAREER_SUBJECT_ID, $studentApprovedCareerSubject->getId());

    return self::doSelectOne($criteria);
  }
}