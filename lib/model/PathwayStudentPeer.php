<?php

class PathwayStudentPeer extends BasePathwayStudentPeer
{

	public static function getStudentsForSchoolYear($school_year) {
		$c = new Criteria();
		$c->addJoin(PathwayPeer::SCHOOL_YEAR_ID, $school_year->getId());

		$values = array();
		foreach (self::doSelect($c) as $sp)
		{
			$values[] = $sp->getStudent();
		}

		return $values;
	}

	public static function retrieveByStudentAndSchoolYear($student_id, $school_year_id = null) {
		if (is_null($school_year_id)) {
			$school_year_id = SchoolYearPeer::retrieveCurrent()->getId();
		}
		$c = new Criteria();
		$c->add(PathwayStudentPeer::STUDENT_ID, $student_id);
		$c->addJoin(PathwayPeer::SCHOOL_YEAR_ID, $school_year_id);

		return self::doSelectOne($c);
	}
}