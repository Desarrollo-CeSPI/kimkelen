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
 * career_subject_option module configuration.
 *
 * @package    sistema de alumnos
 * @subpackage career_subject_option
 * @author     Your name here
 * @version    SVN: $Id: configuration.php 12474 2008-10-31 10:41:27Z fabien $
 */
class career_subject_optionGeneratorConfiguration extends BaseCareer_subject_optionGeneratorConfiguration
{
   public function getUser()
  {
      return sfContext::getInstance()->getUser();
  }

  public function getForm($object = null)
  {
    if ( is_null ($object))
    {
      $object = new CareerSubject();
      $object->setCareerId($this->getUser()->getReferenceFor('career'));
    }

    return new CareerSubjectOptionForm($object);
  }
}