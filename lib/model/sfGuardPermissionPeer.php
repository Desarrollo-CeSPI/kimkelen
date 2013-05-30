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

class sfGuardPermissionPeer extends BasesfGuardPermissionPeer
{
  /**
   * Returns all credentials (permission names) for the role given
   */
  public static function retrieveAllCredentialsForARole($role)
  {
    $c = new Criteria();
    $c->add(sfGuardGroupPermissionPeer::GROUP_ID, $role->getId());
    $c->addJoin(sfGuardGroupPermissionPeer::PERMISSION_ID, self::ID);
    $c->clearSelectColumns();
    $c->addSelectColumn(self::NAME);
    $stmt = self::doSelectStmt($c);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }

  public static function getChoices(){

    $permissions = self::doSelect(new Criteria());
    $choice = array();
    /* @var $permission sfGuardPermission */
    foreach ($permissions as $permission){
      $choice[$permission->getId()] = $permission->getDescription();
    }
    return $choice;
  }
}