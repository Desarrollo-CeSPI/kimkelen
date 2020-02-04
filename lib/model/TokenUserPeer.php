<?php

class TokenUserPeer extends BaseTokenUserPeer
{
	public static function retrieveByToken($token, PropelPDO $con = null)
	{
		$c = new Criteria();
		$c->add(self::TOKEN, $token);
		return self::doSelectOne($c);
  }

  public static function deleteUsedTokenFor($user) {
	  $c = new Criteria();
	  $c->add(self::SF_GUARD_USER_ID, $user->getId());

	  self::doDelete($c);
  }
}