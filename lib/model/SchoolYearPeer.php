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

class SchoolYearPeer extends BaseSchoolYearPeer
{
  /**
   * This method return the actual SchoolYear, is actual if the STATE od the SchoolYear is true
   *
   * @return SchoolYear
   */
  public static function retrieveCurrent(){
    $c = new Criteria();
    $c->add(SchoolYearPeer::IS_ACTIVE, true, Criteria::EQUAL);
    return SchoolYearPeer::doSelectOne($c);
  }

  /**
   * This method deactive all the SchoolYear (put STATE's of the school years in false)
   */
  public static function setAllUnactive(){
    foreach(SchoolYearPeer::doSelect(new Criteria()) as $year){
      $year->setIsActive(false);
      $year->save();
    }
  }

  /**
   * This method returns and integer that represents the sugest year to used on the creation of the school year.
   * For example if the actual school year is 2010, this method will returns 2011
   *
   * @return integer
   */
  public static function sugestYear()
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(self::YEAR);
    if ($school_year = self::doSelectOne($c))
    {
      return $school_year->getYear() + 1;
    }
    else
    {
      return date('Y');
    }
  }

  static public function retrieveLastYearSchoolYear($school_year)
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(self::YEAR);
    $c->add(self::ID, $school_year->getId(), Criteria::LESS_THAN);

    return self::doSelectOne($c);
  }

  static public function retrieveLastYearSchoolYears($school_year)
  {
    $c = new Criteria();
    $c->addDescendingOrderByColumn(self::YEAR);
    $c->add(self::ID, $school_year->getId(), Criteria::LESS_THAN);

    return self::doSelect($c);
  }
}