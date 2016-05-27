<?php

class ChangeStatusMotivePeer extends BaseChangeStatusMotivePeer
{
	public static function getMotivesByStatusId($status_id)
	{
		$c = new Criteria();
		$c->add(ChangeStatusMotivePeer::STATUS_ID, $status_id);
		return self::doSelect($c);
	}
}
