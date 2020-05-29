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

class CourseSubjectStudentMarkPeer extends BaseCourseSubjectStudentMarkPeer
{
  static function retrieveByCourseSubjectStudent($css_id)
  {
    $c = new Criteria();
    $c->add(self::COURSE_SUBJECT_STUDENT_ID, $css_id);
    return self::doSelect($c);
  }
  static function deleteByCourseSubjectStudent($course_subject_student_id , $con)
  {
    $c = new Criteria();
    $c->add(self::COURSE_SUBJECT_STUDENT_ID, $course_subject_student_id);
    self::doDelete($c,$con);
  }
  static function retrieveByCourseSubjectStudentAndPeriod($css_id,$period)
  {
    $c = new Criteria();
    $c->add(self::COURSE_SUBJECT_STUDENT_ID, $css_id);
    $c->add(self::MARK_NUMBER, $period);
    return self::doSelectOne($c);
  }

}
