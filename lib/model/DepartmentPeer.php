<?php

class DepartmentPeer extends BaseDepartmentPeer
{
	public static function retrieveByStateId($state_id)
	{
		$c = new Criteria();
		$c->add(self::STATE_ID, $state_id);
		$c->addAscendingOrderByColumn('name');
		
		return self::doSelect($c);
	
	}
}
