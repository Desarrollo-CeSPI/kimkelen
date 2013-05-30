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

class PersonalType extends BaseCustomOptionsHolder
{
  const
  PRECEPTOR = 1,
  HEAD_PRECEPTOR = 2,
  STUDENTS_OFFICE = 3;

  protected
  $_options = array(
    self::PRECEPTOR => 'Preceptor',
    self::HEAD_PRECEPTOR => 'Jefe de preceptores',
    self::STUDENTS_OFFICE => 'Oficina de alumnos'
  );

  /**
   * Get the options for PersonalType reference class.
   *
   * @return array
   */
  public static function getOptionsInArray()
  {
    return array(
      self::PRECEPTOR => 'Preceptor',
      self::HEAD_PRECEPTOR => 'Jefe de preceptores',
      self::STUDENTS_OFFICE => 'Oficina de alumnos'
    );
  }

  /**
   * Get the string representation of the option identified by $identifier
   * for PersonalType reference class, or null if it's not a valid identifier.
   *
   * @return string or null
   */
  public static function toString($identifier)
  {
    $options = self::getOptionsInArray();

    if (array_key_exists($identifier, $options))
    {
      return $options[$identifier];
    }
  }

}