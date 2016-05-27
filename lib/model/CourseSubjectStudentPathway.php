<?php

class CourseSubjectStudentPathway extends BaseCourseSubjectStudentPathway
{
  public function countValidCourseSubjectStudentPathwayMarks()
  {
    $c = new Criteria();
    $c->add(CourseSubjectStudentPathwayPeer::STUDENT_ID, $this->getStudent()->getId());
    $c->add(CourseSubjectStudentPathwayPeer::COURSE_SUBJECT_ID , $this->getCourseSubject()->getId());
    $c->add(CourseSubjectStudentPathwayPeer::MARK, null, Criteria::ISNOTNULL);

    return CourseSubjectStudentPathwayPeer::doCount($c);

  }

	public function getRelatedCourseSubjectStudent() {

		$c = new Criteria();
		$c->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID, Criteria::INNER_JOIN);
		$c->add(CourseSubjectStudentPeer::STUDENT_ID, $this->getStudentId());
		$c->add(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID, $this->getCourseSubject()->getCareerSubjectSchoolYearId());

		return CourseSubjectStudentPeer::doSelectOne($c);
	}


}
