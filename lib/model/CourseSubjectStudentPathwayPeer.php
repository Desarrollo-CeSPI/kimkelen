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
}
