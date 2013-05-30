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

class CourseSubjectStudentExaminationPeer extends BaseCourseSubjectStudentExaminationPeer
{
  public static function retrieveForExaminationSubject(ExaminationSubject $examination_subject, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;
    
    $c = new Criteria();
    
    $c->addJoin(self::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $examination_subject->getCareerSubjectSchoolYearId());
    
    $c->addJoin(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, CareerSchoolYearPeer::ID);
    
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $examination_subject->getExamination()->getSchoolYearId());
    $c->add(self::EXAMINATION_NUMBER, $examination_subject->getExamination()->getExaminationNumber());
    
    $c->add(self::EXAMINATION_SUBJECT_ID, null, Criteria::ISNULL);
    
    return self::doSelect($c, $con);
  }
  
  public static function retrieveForStudentAndCareerSchoolYear(Student $student, CareerSchoolYear $career_school_year, PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;
    
    $c = new Criteria();
    
    $c->addJoin(self::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
    $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
    
    $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
    $c->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
    
    return self::doSelect($c, $con);
  }

  public static function retrieveLastByCourseSubjectStudentId($cssid)
  {
    $criteria = new Criteria();
    $criteria
        ->addJoin(CourseSubjectStudentExaminationPeer::EXAMINATION_SUBJECT_ID, ExaminationSubjectPeer::ID)
        ->addJoin(ExaminationSubjectPeer::EXAMINATION_ID, ExaminationPeer::ID)
        ->add(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID, $cssid)
        ->addDescendingOrderByColumn(ExaminationPeer::DATE_TO)
    ;

    return self::doSelectOne($criteria);
  }

  static public function retrieveCriteriaForCourseSubjectAndExaminationNumber(CourseSubject $course_subject, $examination_number)
  {
    $c = new Criteria();
    $c->add(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, $course_subject->getId());
    $c->addJoin(CourseSubjectStudentPeer::ID, self::COURSE_SUBJECT_STUDENT_ID);
    $c->add(self::EXAMINATION_NUMBER, $examination_number);
    
    $c->addJoin(self::EXAMINATION_SUBJECT_ID, ExaminationSubjectPeer::ID);
    $c->addJoin(ExaminationSubjectPeer::EXAMINATION_ID, ExaminationPeer::ID);

    return $c;
  }
}