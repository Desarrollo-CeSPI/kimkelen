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

class Subject extends BaseSubject
{
  /**
   * This function redefines the toString() method of the subject
   *
   * @see parent::__toString()
   *
   * @return string
   */
  public function __toString()
  {
    return SchoolBehaviourFactory::getInstance()->getSubjectToString($this);

  }


  /**
   * Answer if this Subject has no relationships that prevent it from being safely deleted.
   *
   * @param PropelPDO $con
   *
   * @return boolean Whether this Subject can be deleted.
   */
  public function canBeDeleted(PropelPDO $con = null)
  {
    return ($this->countCareerSubjects() == 0);
  }
}