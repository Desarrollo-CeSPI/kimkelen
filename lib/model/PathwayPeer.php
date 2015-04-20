<?php

class PathwayPeer extends BasePathwayPeer
{
	public static function retrieveCurrent(){
		$c = new Criteria();
		$c->add(PathwayPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId(), Criteria::EQUAL);

		return PathwayPeer::doSelectOne($c);
	}
}