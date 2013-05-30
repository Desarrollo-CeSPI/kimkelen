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


class UserProfile
{
  const ADMIN     = 1;
  const PRECEPTOR = 2;
  const PROFESOR  = 3;
  const STUDENT   = 4;

  protected static $strings= array(
    self::ADMIN     => 'Administrador',
    self::PRECEPTOR => 'Preceptor',
    self::PROFESOR  => 'Profesor',
    self::STUDENT   => 'Alumno',
  );

  static function getStateString($state)
  {
    return self::$strings[$state];
  }
  
  static function getOptionsForSelect()
  {
    return self::$strings;
  }

 }