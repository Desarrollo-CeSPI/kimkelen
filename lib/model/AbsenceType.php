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

class AbsenceType extends BaseAbsenceType
{

  public function __toString()
  {
    return $this->getName();

  }

  public function getMethodStr()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));
    return ($this->method == AbsenceMethod::SUBJECT) ? __('Per subject') : __('Per day');

  }

  public function incrementOrder()
  {
   return $this->setOrder($this->getOrder() + 1);

  }

  public function decrementOrder()
  {
    return $this->setOrder($this->getOrder() - 1);

  }

  public function canBeDeleted(PropelPDO $con = null)
  {
    $criteria = new Criteria();
    $criteria->add(StudentAttendancePeer::ABSENCE_TYPE_ID, $this->getId());
    
    return !(StudentAttendancePeer::doCount($criteria));
  }

}