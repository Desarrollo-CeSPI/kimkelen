<?php

class TentativeRepprovedStudentPeer extends BaseTentativeRepprovedStudentPeer
{
	public static function countPending() {
		$c = new Criteria();
		$c->add(self::IS_DELETED, false, Criteria::EQUAL);

		return self::doCount($c);
	}
}