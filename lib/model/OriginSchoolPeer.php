<?php

class OriginSchoolPeer extends BaseOriginSchoolPeer
{
	public static function retrieveByCityId($city_id)
	{
		$c = new Criteria();
		$c->add(self::CITY_ID, $city_id);
		$c->addAscendingOrderByColumn(self::NAME);
		
		return self::doSelect($c);
	
	}
}


