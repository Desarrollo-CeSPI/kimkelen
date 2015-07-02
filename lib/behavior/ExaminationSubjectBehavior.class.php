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

/**
 * @author pmacadden
 */
class ExaminationSubjectBehavior
{
  private static $_enabled = true;

  public static function enabled()
  {
    return ExaminationSubjectBehavior::$_enabled;
  }

  public static function disable()
  {
    ExaminationSubjectBehavior::$_enabled = false;
  }

  public static function enable()
  {
    ExaminationSubjectBehavior::$_enabled = true;
  }
  /**
   * Adds the reference for the ExaminationSubject to the CourseSubjectStudentExaminations.
   *
   * @param ExaminationSubject $examination_subject
   * @param PropelPDO $con
   */
  public function updateCourseSubjectStudentExaminations($examination_subject, PropelPDO $con)
  {
    //this is done for manual examinations
    if (!ExaminationSubjectBehavior::enabled())
    {
      return;
    }

    $course_subject_student_examinations = CourseSubjectStudentExaminationPeer::retrieveForExaminationSubject($examination_subject);

    foreach ($course_subject_student_examinations as $course_subject_student_examination)
    {
      $examination_subject->addCourseSubjectStudentExamination($course_subject_student_examination);
    }
  }
}
