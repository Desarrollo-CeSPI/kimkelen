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
 * @author pmacadden
 */
class StudentRepprovedCourseSubjectBehavior
{
  public function checkRepeatition($student_repproved_course_subject, PropelPDO $con = null)
  {
    /*
    $con = is_null($con) ? PropelPDO::getConnection() : $con;
    
    $student = $student_repproved_course_subject->getCourseSubjectStudent()->getStudent();
    
    $career_school_year = $student_repproved_course_subject->getCourseSubjectStudent()->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSchoolYear();
    
    $count = StudentRepprovedCourseSubjectPeer::countRepprovedForStudentAndCareer($student, $career_school_year->getCareer(), $con);
    
    if ($count > $career_school_year->getSubjectConfiguration()->getMaxPrevious())
    {      
      // se obtiene el student_career_school_year
      $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $career_school_year);
      
      // se marca como que el alumno repitió el año
      $student_career_school_year->setStatus(StudentCareerSchoolYearStatus::REPPROVED);
      $student_career_school_year->save($con);
      
      // se eliminan las materias aprobadas
      $c = new Criteria();
      $c->add(StudentApprovedCareerSubjectPeer::STUDENT_ID, $student->getId());
      $c->add(StudentApprovedCareerSubjectPeer::SCHOOL_YEAR_ID, $career_school_year->getSchoolYearId());
      StudentApprovedCareerSubjectPeer::doDelete($c, $con);
      
      // se actualizan las materias desaprobadas con can_take_examination
      $course_subject_student_examinations = CourseSubjectStudentExaminationPeer::retrieveForStudentAndCareerSchoolYear($student, $career_school_year, $con);
      
      // se setean en false todas, total no puede rendir las que ya rindió...
      foreach ($course_subject_student_examinations as $course_subject_student_examination)
      {
        $course_subject_student_examination->setCanTakeExamination(false);
        $course_subject_student_examination->save($con);
      }
      
      // se eliminan las previas
      $c = new Criteria();
      $c->addJoin(StudentRepprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
      $c->add(CourseSubjectStudentPeer::STUDENT_ID, $student->getId());
      $c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
      $c->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, CareerSubjectSchoolYearPeer::ID);
      $c->add(CareerSubjectSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $career_school_year->getId());
      
      foreach (StudentRepprovedCourseSubjectPeer::doSelect($c, $con) as $repproved)
      {
        $repproved->delete($con);
      }

      $c = new Criteria();
      $c->addJoin(CourseSubjectStudentExaminationPeer::COURSE_SUBJECT_STUDENT_ID,  CourseSubjectStudentPeer::ID);
      $c->add(CourseSubjectStudentPeer::STUDENT_ID,$student->getId());
      
      foreach (CourseSubjectStudentExaminationPeer::doSelect($c) as $csse)
      {
        $csse->setCanTakeExamination(false);
      }
    }
    */
  }
}