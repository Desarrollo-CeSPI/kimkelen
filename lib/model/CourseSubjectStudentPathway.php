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

	protected function doSave(PropelPDO $con)
	{
		try
		{
			$con->beginTransaction();
			if ($this->countCourseSubjectStudentPathwayMarks() == 0)
			{
				for ($i = 1; $i <= $this->getCourseSubject()->getCareerSubjectSchoolYear()->getConfiguration()->getCourseMarks(); $i++)
				{
					$course_subject_student_mark = new CourseSubjectStudentPathwayMark();
					$course_subject_student_mark->setCourseSubjectStudentPathway($this);
					$course_subject_student_mark->setMarkNumber($i);
					$course_subject_student_mark->save($con);
				}
			}
			parent::doSave($con);
			$con->commit();
		}
		catch (PropelException $e)
		{
			$con->rollBack();
		}

	}
}
