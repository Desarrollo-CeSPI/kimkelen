<?php

class TentativeRepprovedStudentPeer extends BaseTentativeRepprovedStudentPeer
{
	public static function countPending() {
		$c = new Criteria();
		$c->add(self::IS_DELETED, false, Criteria::EQUAL);

		return self::doCount($c);
	}

	public static function getStudents() {

		$c = new Criteria();
		$c->add(self::IS_DELETED, false, Criteria::EQUAL);
		$c->addJoin(self::STUDENT_CAREER_SCHOOL_YEAR_ID, StudentCareerSchoolYearPeer::ID, Criteria::INNER_JOIN);
		$c->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
		$c->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID, Criteria::INNER_JOIN);
		$c->addAscendingOrderByColumn(PersonPeer::LASTNAME);

		$values = array();
		foreach (self::doSelect($c) as $s)
		{
			$values[$s->getId()] = $s;
		}

		return $values;
	}

	public static function retrieveByStudentId($student_id) {
		$c = new Criteria();
    $student = StudentPeer::retrieveByPK($student_id);
		$scsy = StudentCareerSchoolYearPeer::retrieveCareerSchoolYearForStudentAndYear($student, SchoolYearPeer::retrieveCurrent());

		$c->add(self::STUDENT_CAREER_SCHOOL_YEAR_ID, $scsy[0]->getId());
		$c->add(self::IS_DELETED, true, Criteria::EQUAL);

		return self::doSelectOne($c);
	}


	public static function doSelectNonDeleted() {
		$c = new Criteria();
		$c->add(self::IS_DELETED, false, Criteria::EQUAL);

		return self::doSelect($c);
	}
}