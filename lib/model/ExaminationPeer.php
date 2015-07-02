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

class ExaminationPeer extends BaseExaminationPeer
{
  /**
   * Returns the next examination number for the given school year.
   *
   * @param SchoolYear $school_year A school year.
   * @return integer The next examation number.
   */
  public static function getNextExaminationNumberFor(SchoolYear $school_year)
  {
    $c = new Criteria();
    $c->add(self::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addDescendingOrderByColumn(self::EXAMINATION_NUMBER);

    $obj = self::doSelectOne($c);

    return $obj->getExaminationNumber() + 1;
  }

  static public function retrieveForSchoolYearAndExaminationNumber($school_year, $examination_number)
  {
    $c = new Criteria();
    $c->add(ExaminationPeer::EXAMINATION_NUMBER, $examination_number);
    $c->add(self::SCHOOL_YEAR_ID, $school_year->getId());

    return self::doSelect($c);
  }
}