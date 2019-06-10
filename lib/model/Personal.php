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

class Personal extends BasePersonal
{

  /**
   * This method implements __toString method to print the object
   *
   * @return string
   */
  public function __toString()
  {
    return $this->getPersonFullName();

  }

  /**
   * Proxies getPerson()->$method as getPersonMethod in current object. Only for getters
   *
   * @param string $method
   * @param <type> $arguments
   * @return <type>
   */
  public function __call($method, $arguments)
  {

    if (preg_match('/^getPerson(.*)/', $method, $matches) && isset($matches[1]))
    {
      $method = "get" . $matches[1];
      return $this->getPerson()->$method();
    }
    if (preg_match('/^canPersonBe(.*)/', $method, $matches) && isset($matches[1]))
    {
      $method = "canBe" . $matches[1];
      return $this->getPerson()->$method();
    }
    parent::__call($method, $arguments);

  }

  public function getHeadPersonalPersonals()
  {
    $c = new Criteria();
    $c->add(HeadPersonalPersonalPeer::HEAD_PERSONAL_ID, $this->getId());

    return HeadPersonalPersonalPeer::doSelect($c);

  }

  public function createTeacher(PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    $teacher = new Teacher();
    $teacher->setPerson($this->getPerson());
    $teacher->save($con);

    $guard_user = $this->getPersonSfGuardUser();
    if (!is_null($guard_user))
    {
      $teacher_group = BaseCustomOptionsHolder::getInstance('GuardGroups')->getStringFor(GuardGroups::TEACHER);
      if (!array_key_exists($teacher_group, $guard_user->getGroups()))
      {
        $guard_user->addGroupByName($teacher_group);
        $guard_user->save($con);
      }
    }

  }

  public function canAddTeacher()
  {
    $c = new Criteria();
    $c->add(TeacherPeer::PERSON_ID, $this->getPersonId());

    return TeacherPeer::doCount($c) == 0;

  }

  public function getMessageCantAddTeacher()
  {
    return 'The preceptor is already a teacher.';

  }

  public function isPreceptor()
  {
    return $this->getPersonalType() == PersonalType::PRECEPTOR;

  }

  public function canBeDeleted()
  {
    return ($this->isPreceptor());
  }

  /**
   * Returns corresponding show route for this type of personal
   *
   * @return string
   */
  public function retrieveRouteForShow()
  {
    switch ($this->getPersonalType())
    {
      case PersonalType::PRECEPTOR:
        $value = '@personal_show';
        break;
      case PersonalType::HEAD_PRECEPTOR:
        $value = '@head_personal_show';
        break;
      case PersonalType::STUDENTS_OFFICE:
        $value = '@student_office_show';
        break;
    }

    return $value;

  }

  public function getEmail()
  {
    return $this->getPerson()->getEmail();

  }

  public function getPhone()
  {
    return $this->getPerson()->getPhone();

  }

    public function getMessageCantBeDeleted()
  {
    return "User has some references you sholud delete first";
  }

  public function canPersonBeActivated()
  {
    return $this->getPerson()->getIsActive() == false;

  }

  public function canPersonBeDeactivated()
  {
    return $this->getPerson()->getIsActive() == true;

  }
  
  public function canAddTutor()
  {
    $c = new Criteria();
    $c->add(TutorPeer::PERSON_ID, $this->getPersonId());

    return TutorPeer::doCount($c) == 0;

  }

}

sfPropelBehavior::add('Personal', array('person_delete'));