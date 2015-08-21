<?php

class CourseSubjectStudentPathway extends BaseCourseSubjectStudentPathway
{
  public function countValidCourseSubjectStudentPathwayMarks()
  {
    $c = new Criteria();
    $criterion = $c->getNewCriterion(CourseSubjectStudentPathwayPeer::MARK, null, Criteria::ISNOTNULL);

    $c->addOr($criterion);
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
