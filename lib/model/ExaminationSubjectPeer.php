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

class ExaminationSubjectPeer extends BaseExaminationSubjectPeer
{
  public static function countNotClosedExaminationSubjectsFor(SchoolYear $school_year, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;
    
    $c = new Criteria();
    $c->add(ExaminationPeer::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addJoin(ExaminationSubjectPeer::EXAMINATION_ID, ExaminationPeer::ID);
    $c->add(ExaminationSubjectPeer::IS_CLOSED, false);
    
    return ExaminationSubjectPeer::doCount($c, $con);

  }
  
  public static function retrieveByCourseSubjectStudentExamination(CourseSubjectStudentExamination $course_subject_student_examination, $con = null)
  {
    $criteria = new Criteria();
    $criteria->add(self::CAREER_SUBJECT_SCHOOL_YEAR_ID, $course_subject_student_examination->getCourseSubject()->getCareerSubjectSchoolYearId());
    $criteria->addJoin(self::EXAMINATION_ID, ExaminationPeer::ID);
    $criteria->add(ExaminationPeer::EXAMINATION_NUMBER, $course_subject_student_examination->getExaminationNumber());
    $criteria->add(ExaminationPeer::SCHOOL_YEAR_ID, $course_subject_student_examination->getCourseSubject()->getCareerSubjectSchoolYear()->getSchoolYear()->getId());
    return self::doSelectOne($criteria, $con);
  }
}