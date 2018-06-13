<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGuardUserPeer.php 9999 2008-06-29 21:24:44Z fabien $
 */
class sfGuardUserPeer extends PluginsfGuardUserPeer
{
	public static function retrieveByUsernameOrEmail($usernameOrEmail, $isActive = true)
	{
		$c = new Criteria();
		$c->addJoin(self::ID, sfGuardUserProfilePeer::USER_ID, Criteria::LEFT_JOIN);
		$c0 = $c->getNewCriterion(self::USERNAME, $usernameOrEmail);
		$c1 = $c->getNewCriterion(sfGuardUserProfilePeer::EMAIL, $usernameOrEmail);
		$c2 = $c->getNewCriterion(self::IS_ACTIVE, $isActive);
		$c0->addOr($c1);
		$c0->addAnd($c2);
		$c->add($c0);

		return self::doSelectOne($c);
	}
}