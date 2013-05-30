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

class CourseSubjectDayPeer extends BaseCourseSubjectDayPeer
{
  static public function retrieveByDayAndBlockAndCourseSubjectId($day, $block, $course_subject_id)
  {
    $c = new Criteria();
    $c->add(self::COURSE_SUBJECT_ID,$course_subject_id);
    $c->add(self::BLOCK,$block);
    $c->add(self::DAY,$day);
    return self::doSelectOne($c);
  }

  static public function retrieveOrCreateByDayAndBlockAndCourseSubjectId($day, $block, $course_subject_id)
  {
    $course_subject_day = self::retrieveByDayAndBlockAndCourseSubjectId($day, $block, $course_subject_id);

    if (is_null($course_subject_day)){
      $course_subject_day = new CourseSubjectDay();
      $course_subject_day->setDay($day);
      $course_subject_day->setBlock($block);
      $course_subject_day->setCourseSubjectId($course_subject_id);
    }

    return $course_subject_day;
  }
}