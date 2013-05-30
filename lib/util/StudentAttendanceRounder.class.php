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

class StudentAttendanceRounder
{
  const TROUBLED_VALUE_ONE_THIRD   = "0.33";
  const TROUBLED_VALUE_ONE_SIXTH   = "0.16";
  const TROUBLED_VALUE_ONE_SEVENTH = "0.14";
  const TROUBLED_VALUE_ONE_EIGHTH  = "0.12";
  const TROUBLED_VALUE_ONE_NINTH   = "0.11";

  static $roundTable = array(
      self::TROUBLED_VALUE_ONE_THIRD   => array('countAbsencesDiff' => 3, 'diff' => 0.01),
      self::TROUBLED_VALUE_ONE_SIXTH   => array('countAbsencesDiff' => 6, 'diff' => 0.04),
      self::TROUBLED_VALUE_ONE_SEVENTH => array('countAbsencesDiff' => 7, 'diff' => 0.02),
      self::TROUBLED_VALUE_ONE_EIGHTH  => array('countAbsencesDiff' => 8, 'diff' => 0.04),
      self::TROUBLED_VALUE_ONE_NINTH   => array('countAbsencesDiff' => 9, 'diff' => 0.01),
  );
  
  protected $counts = array(
      self::TROUBLED_VALUE_ONE_THIRD   => 0,
      self::TROUBLED_VALUE_ONE_SIXTH   => 0,
      self::TROUBLED_VALUE_ONE_SEVENTH => 0,
      self::TROUBLED_VALUE_ONE_EIGHTH  => 0,
      self::TROUBLED_VALUE_ONE_NINTH   => 0,
  );

  public function calculateDiff()
  {
    $total = 0;
    foreach ($this->counts as $key => $count)
    {
      $total += ((((Integer) ($count / self::$roundTable[$key]['countAbsencesDiff'])) * self::$roundTable[$key]['diff']));
    }
    
    return $total;
  }

  public function process($absence)
  {
    if (array_key_exists((String) $absence->getValue(), self::$roundTable))
    {
      $this->counts[(String) $absence->getValue()]++;
    }
  }

}