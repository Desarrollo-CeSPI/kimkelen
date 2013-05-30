<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php

class sfGuardUserGroupPeer extends BasesfGuardUserGroupPeer
{
  /**
   *Returns all roles (groups) $user_id has, excepting current one $current_role
   *
   */
  static public function retrieveForUserWithoutCurrentRole($current_role, $user_id)
  {
    $c = new Criteria();
    $c->add(self::USER_ID, $user_id);
    $c->addJoin(self::GROUP_ID, sfGuardGroupPeer::ID);
    $c->add(sfGuardGroupPeer::NAME, $current_role, Criteria::NOT_EQUAL);

    return sfGuardGroupPeer::doSelect($c);
  }

}