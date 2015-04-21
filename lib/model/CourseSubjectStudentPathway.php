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
}
