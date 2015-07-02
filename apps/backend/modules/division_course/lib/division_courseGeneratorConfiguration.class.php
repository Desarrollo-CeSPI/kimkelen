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

/**
 * division_course module configuration.
 *
 * @package    sistema de alumnos
 * @subpackage division_course
 * @author     Your name here
 * @version    SVN: $Id: configuration.php 12474 2008-10-31 10:41:27Z fabien $
 */
class division_courseGeneratorConfiguration extends BaseDivision_courseGeneratorConfiguration
{
  public function getForm($object = null)
  {
    $class = SchoolBehaviourFactory::getInstance()->getFormFactory()->getCourseForm();
    
    return new $class($object, $this->getFormOptions());
  }

  public function getFilterFormClass()
  {
    return SchoolBehaviourFactory::getInstance()->getFormFactory()->getDivisionCourseFormFilter();
  }
}