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

class SchoolYearStudentPeer extends BaseSchoolYearStudentPeer
{
  /**
   * retrieve the students actived at $school_year
   * @param SchoolYear $school_year
   *
   * @return array [Student]
   */
  static public function retrieveStudentsForSchoolYearCriteria(SchoolYear $school_year)
  {
    $c = new Criteria();
    $c->add(self::SCHOOL_YEAR_ID, $school_year->getId());
    $c->addJoin(self::STUDENT_ID, StudentPeer::ID);
    self::clearInstancePool();
    return $c;

  }

  /**
   * retrieve the students actived at $school_year
   * @param SchoolYear $school_year
   *
   * @return array [Student]
   */
  static public function retrieveStudentsForSchoolYear(SchoolYear $school_year)
  {
    $students = StudentPeer::doSelect(self::retrieveStudentsForSchoolYearCriteria($school_year));

    StudentPeer::clearInstancePool();
    return $students;
  }

  static public function retrieveStudentIdsForSchoolYear(SchoolYear $school_year)
  {
    $c = self::retrieveStudentsForSchoolYearCriteria($school_year);
    $ids = array();
    foreach (self::doSelect($c) as $sys)
    {
      $ids[] = $sys->getStudentId();
    }

    return $ids;
  }
}