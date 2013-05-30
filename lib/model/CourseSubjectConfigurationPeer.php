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

class CourseSubjectConfigurationPeer extends BaseCourseSubjectConfigurationPeer
{

  public static function retrieveBySubject($course_subject)
  {
    $c = new Criteria();
    $c->add(self::COURSE_SUBJECT_ID, $course_subject->getId());

    return self::doSelect($c);

  }

  public static function retrieveByDivisionAndPeriod($division_id, $career_school_year_period_id)
  {
    $c = new Criteria();
    $c->add(self::DIVISION_ID, $division_id);
    $c->add(self::CAREER_SCHOOL_YEAR_PERIOD_ID, $career_school_year_period_id);
    return self::doSelectOne($c);

  }

  public static function HasConfiguration($division_id)
  {
    $c = new Criteria();
    $c->add(self::DIVISION_ID, $division_id);

    return self::doCount($c) > 0;

  }

}