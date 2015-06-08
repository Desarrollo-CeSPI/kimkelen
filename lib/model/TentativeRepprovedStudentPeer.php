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

		foreach (self::doSelect($c) as $s)
		{
			$values[] = $s->getStudentCareerSchoolYear()->getStudent();
		}

		return $values;
	}

	public static function doSelectNonDeleted() {
		$c = new Criteria();
		$c->add(self::IS_DELETED, false, Criteria::EQUAL);

		return self::doSelect($c);
	}
}