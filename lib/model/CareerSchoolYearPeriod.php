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

class CareerSchoolYearPeriod extends BaseCareerSchoolYearPeriod
{
  public function __toString()
  {
    return sprintf('%s (%s)', $this->getName() , $this->getTermStr());
  }

  public function getTermStr()
  {
    return sprintf('%s a %s', date('d-m-Y',strtotime($this->getStartAt())), date('d-m-Y',strtotime($this->getEndAt())));
  }

  public function getCourseTypeStr()
  {
    $choices = SchoolBehaviourFactory::getInstance()->getCourseTypeChoices();

    return $choices[$this->getCourseType()];
  }

  public function isQuaterly()
  {
    return $this->getCourseType() == CourseType::QUATERLY;
  }

  public function isBimester()
  {
    return $this->getCourseType() == CourseType::BIMESTER;
  }

  public function close()
  {
    $this->setIsClosed(true);
    $this->save();
  }

  public function open()
  {
    $this->setIsClosed(false);
    $this->save();
  }

  public function canClose()
  {
    return !$this->getIsClosed();
  }

  public function canOpen()
  {
    return $this->getIsClosed();
  }
}