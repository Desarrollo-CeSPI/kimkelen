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

class LetterMark extends BaseCustomOptionsHolder
{
  // usar esto para instanciar BaseCustomOptionsHolder::getInstance('LetterMark')->getOption()
  const MARK_FREE = 0;
  const MARK_A = 2;
  const MARK_S = 4;
  const MARK_B = 6;
  const MARK_D = 8;
  const MARK_E = 10;

  protected
    $_options = array(
        self::MARK_FREE => 'Libre',
        self::MARK_A => 'A',
        self::MARK_S => 'S',
        self::MARK_B => 'B',
        self::MARK_D => 'D',
        self::MARK_E => 'E',
    );
    
  public static function getOption($key)
  {
    $mark = round($key);
    if ($mark % 2 != 0)
    {
      $mark++;
    }

    $array = self::getOptionsInArray();
    return $array[$mark];
  }

  public static function getOptionsInArray()
  {
    return array(
        self::MARK_FREE => 'Libre',
        self::MARK_A => 'A',
        self::MARK_S => 'S',
        self::MARK_B => 'B',
        self::MARK_D => 'D',
        self::MARK_E => 'E',
    );
  }
}