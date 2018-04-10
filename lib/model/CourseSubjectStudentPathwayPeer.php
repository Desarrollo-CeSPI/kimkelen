<?php

class CourseSubjectStudentPathwayPeer extends BaseCourseSubjectStudentPathwayPeer
{
  public static function countStudentInscriptionsForCareerSubjectSchoolYear($course_subject_id, $student_id)
  {
    $course_subject = CourseSubjectPeer::retrieveByPk($course_subject_id);
    $c = new Criteria();
    $c->addJoin(CourseSubjectStudentPathwayPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
    $c->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $course_subject->getCareerSubjectSchoolYearId());
    $c->add(CourseSubjectStudentPathwayPeer::STUDENT_ID, $student_id);
    $c->addAnd(CourseSubjectPeer::ID, $course_subject_id, Criteria::NOT_EQUAL);

    return CourseSubjectStudentPathwayPeer::doCount($c);
  }
  
  public static function retrieveStudentsByCourseSubject($course_subject)
  {
      $c = new Criteria();
      $c->add(self::COURSE_SUBJECT_ID,$course_subject->getId());
      $c->addJoin(self::STUDENT_ID, StudentPeer::ID);
      
      return StudentPeer::doSelect($c);
  }
  
  public static function retrieveByCourseSubjectStudent($course_subject_student)
  { 
      
      $c = new Criteria();
      $c->addJoin(CourseSubjectStudentPathwayPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
      $c->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYearId());
      $c->add(self::STUDENT_ID,$course_subject_student->getStudent()->getId());
      return CourseSubjectStudentPathwayPeer::doSelectOne($c);
      
  }
}
