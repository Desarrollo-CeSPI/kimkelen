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

class Tutor extends BaseTutor
{

  /**
   * This method implements __toString method to print the object
   *
   * @return string
   */
  public function __toString(){
    return $this->getPersonLastname().' '.$this->getPersonFirstname();
  }


  /**
   * Proxies getPerson()->$method as getPersonMethod in current object. Only for getters
   *
   * @param string $method
   * @param <type> $arguments
   * @return <type>
   */
  public function __call($method, $arguments) {
    if ( preg_match('/^getPerson(.*)/',$method, $matches)&&isset($matches[1]))
    {
      $method = "get".$matches[1];
      return $this->getPerson()->$method();
    }
     if ( preg_match('/^canPersonBe(.*)/',$method, $matches)&&isset($matches[1]))
    {
      $method = "canBe".$matches[1];
      return $this->getPerson()->$method();
    }
    parent::__call($method, $arguments);
  }

  /**
   * deletes associated students to this tutor
   * @param <type> $con
   */
  public function deleteStudents($con=null)
  {
    if (is_null($con))
      $con = Propel::getConnection();
    $con->beginTransaction();
    try
    {
      foreach ($this->getStudentTutors() as $student_tutor)
      {
        $student_tutor->delete($con);
      }
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
    }
  }

  public function getStudentTutorsString()
  { 
    $students = array();
    foreach ($this->getStudentTutors() as $student_tutor)
    {
      $students[] = $student_tutor->getStudent();
    }

    return implode(',  ', $students);
  }
  
  public function canAddPreceptor()
  {
    $c = new Criteria();
    $c->add(PersonalPeer::PERSON_ID, $this->getPersonId());

    return PersonalPeer::doCount($c) == 0;

  }
  
  public function canAddTeacher()
  {
    $c = new Criteria();
    $c->add(TeacherPeer::PERSON_ID, $this->getPersonId());

    return TeacherPeer::doCount($c) == 0;

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
  
  public function createPreceptor(PropelPDO $con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;

    $personal = new Personal();
    $personal->setPerson($this->getPerson());
    $personal->save($con);

    $guard_user = $this->getPersonSfGuardUser();
    if (!is_null($guard_user))
    {
      $personal_group = BaseCustomOptionsHolder::getInstance('GuardGroups')->getStringFor(GuardGroups::PERSONAL);
      if (!array_key_exists($personal_group, $guard_user->getGroups()))
      {
        $guard_user->addGroupByName($personal_group);
        $guard_user->save($con);
      }
    }

  }
}