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
 * shared_student module configuration.
 *
 * @package    sistema de alumnos
 * @subpackage shared_student
 * @author     Your name here
 * @version    SVN: $Id: configuration.php 12474 2008-10-31 10:41:27Z fabien $
 */
class shared_studentGeneratorConfiguration extends BaseShared_studentGeneratorConfiguration
{
  public function hasFilterForm()
  {
    $reference_array = sfContext::getInstance()->getUser()->getReferenceFor("shared_student");
    
    return isset($reference_array["filter_class"]);
  }

  public function getFilterFormClass()
  {
    $reference_array = sfContext::getInstance()->getUser()->getReferenceFor("shared_student");
    
    return isset($reference_array["filter_class"]) ? $reference_array["filter_class"] : "StudentFormFilter";
  }

  public function getListTitle()
  {    
    $reference_array = sfContext::getInstance()->getUser()->getReferenceFor("shared_student");
    
    return isset($reference_array["title"]) ? $reference_array["title"] : "Student list";
  }

  public function getExportationTitle()
  {
    return $this->getListTitle();
  }

  
}