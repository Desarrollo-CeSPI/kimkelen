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

class CourseSubjectStudentMark extends BaseCourseSubjectStudentMark
{

  public function __toString()
  {

    if ($this->getIsFree())
    {
      return $this->getShortFreeLabel();
    }
    if ($this->getMark())
    {
      return strval($this->getMark());
    }
    else
    {
      return '';
    }

  }

  public function renderChangeLog()
  {
    return ncChangelogRenderer::render($this, 'tooltip', array('credentials' => 'view_changelog'));
  }

  public function getFreeLabel()
  {
    return SchoolBehaviourFactory::getInstance()->getFreeLabel($this);
  }

  public function getShortFreeLabel()
  {
    return SchoolBehaviourFactory::getInstance()->getShortFreeLabel($this);
  }

  public function getStringMark()
  {
    return $this->__toString();
  }

  public function getColor()
  {
    return SchoolBehaviourFactory::getEvaluatorInstance()->getColorForCourseSubjectStudentMark($this);
  }

  public function getMarkByConfig($config = null)
  {
    if ($config != null && !$config->isNumericalMark())
    {
      return BaseCustomOptionsHolder::getInstance('LetterMark')->getOption((Integer)$this->getMark());
    }
    else
    {
      return $this->getMark();
    }
  }

}

sfPropelBehavior::add('CourseSubjectStudentMark', array('changelog'));