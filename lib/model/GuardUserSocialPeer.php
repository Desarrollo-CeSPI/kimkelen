<?php

class GuardUserSocialPeer extends BaseGuardUserSocialPeer
{
	public static function retrieveBySocialId($social_id)
	{
		$c = new Criteria();
		$c->add(GuardUserSocialPeer::SOCIAL_ID,$social_id);
		
		return GuardUserSocialPeer::doSelectOne($c);
	}
}
