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
 * @version    SVN: $Id: sfGuardGroupPeer.php 9999 2008-06-29 21:24:44Z fabien $
 */
class sfGuardGroupPeer extends PluginsfGuardGroupPeer
{

  const ADMIN = 1;
  const PRECEPTOR = 2;
  const PROFESOR = 3;
  const JEFE_PRECEPTOR = 4;
  const OFICINA_ALUMNOS = 5;

  protected static
    $_statii = array(
      self::ADMIN           => "Administrador",
      self::PRECEPTOR       => "Preceptor",
      self::PROFESOR        => "Profesor",
      self::JEFE_PRECEPTOR  => "Jefe de preceptor",
      self::OFICINA_ALUMNOS => "Oficina de alumnos"
    );

  static protected function getNameForGroup($group)
  {
    switch ($group)
    {
      case self::ADMIN:
        return 'administrador';
      case self::PRECEPTOR:
        return 'preceptor';
      case self::PROFESOR:
        return 'profesor';
      case self::JEFE_PRECEPTOR:
        return 'jefe_preceptor';
      case self::OFICINA_ALUMNOS:
        return 'oficina_alumnos';
      default:
        return '';
    }
  }

  static public function isAdministrator($sf_guard_group_id)
  {
    return (self::ADMIN == $sf_guard_group_id);
  }

  static public function isTeacher($sf_guard_group_id)
  {
    return (self::PROFESOR == $sf_guard_group_id);
  }

  static public function isStudent($sf_guard_group_id)
  {
    return (self::ALUMNO == $sf_guard_group_id);
  }

  static public function getGroupById($sf_guard_group_id)
  {
    $c = new Criteria();
    $c->add(self::ID,$sf_guard_group_id,Criteria::EQUAL);
    $group = self::doSelectOne($c);
    if (!is_null($group))
      return $group->getId();
    else
      return null;
  }

  static public function canBeDeleted($id)
  {
    $c = new Criteria();
    $c->add(sfGuardUserGroupPeer::GROUP_ID, $id);
    return !(self::doCount($c));
  }

  static public function retrieveGroups()
  {
    $not = self::personalizedGroups();
    $c = new Criteria();
    $c->add(self::ID, $not, Criteria::NOT_IN);

    return self::doSelect($c);
  }

  static public function personalizedGroups()
  {
    return array(self::PRECEPTOR, self::PROFESOR, self::JEFE_PRECEPTOR, self::OFICINA_ALUMNOS);
  }
}
