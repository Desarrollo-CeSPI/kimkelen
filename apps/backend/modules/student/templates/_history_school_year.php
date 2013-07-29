<?php /*
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
<?php $student = $student_career_school_year->getStudent() ?>
<?php $divisions = $student->getCurrentDivisions($student_career_school_year->getCareerSchoolYearId())
// ver que hacer con las divisiones como distinguirlas entre  las tablas para informar los nombres
?>

<div class="history_school_year">
  <?php if ($student_career_school_year->getStatus() != StudentCareerSchoolYearStatus::WITHDRAWN): ?>
    <h2><?php echo __("School year %%school_year%% | %%career%%", array("%%school_year%%" => $student_career_school_year->getCareerSchoolYear()->getSchoolYear(), "%%career%%" => $student_career_school_year->getCareerSchoolYear()->getCareer())) ?></h2>

    <div class="info_div">
      <strong><?php echo __("Career year") ?></strong> <em><?php echo $student_career_school_year->getYear() ?></em>
    </div>
    <div class="info_div">
      <strong><?php echo __("Status") ?></strong> <em><?php echo $student_career_school_year->getStatusString() ?></em>
    </div>

  <?php $career_school_year = $student_career_school_year->getCareerSchoolYear(); ?>
  <?php $course_subject_students = $student_career_school_year->getCourses(); ?>

  <?php $career_student = CareerStudentPeer::retrieveByCareerAndStudent($career_school_year->getCareerId(), $student->getId()) ?>
  <?php $back_url = isset($back_url) ? $back_url : '' ?>



  <?php
  isset($course_subject_students['ANUAL']) ? include_partial("student/current_course_subjects", array("course_subject_students" => $course_subject_students['ANUAL'],
        'career_student' => $career_student,
        'back_url' => $back_url,
        'student' => $student,
        'course_type' => CourseType::TRIMESTER)) : ''
  ?>

  <?php
  isset($course_subject_students['QUATERLY']) ? include_partial("student/current_course_subjects", array("course_subject_students" => $course_subject_students['QUATERLY'],
        'career_student' => $career_student,
        'back_url' => $back_url,
        'student' => $student,
        'course_type' => CourseType::QUATERLY)) : ''
  ?>

  <?php
  isset($course_subject_students['BIMESTER']) ? include_partial("student/current_course_subjects", array("course_subject_students" => $course_subject_students['BIMESTER'],
        'career_student' => $career_student,
        'back_url' => $back_url,
        'student' => $student,
        'course_type' => CourseType::BIMESTER)) : ''
  ?>

  <?php
  isset($course_subject_students['QUATERLY_OF_A_TERM']) ? include_partial("student/current_course_subjects", array("course_subject_students" => $course_subject_students['QUATERLY_OF_A_TERM'],
        'career_student' => $career_student,
        'back_url' => $back_url,
        'student' => $student,
        'course_type' => CourseType::QUATERLY_OF_A_TERM)) : ''
  ?>

  <?php if ($anual_average = $student_career_school_year->getAnualAverage()): ?>
    <div class="info_div">
      <strong><?php echo __("Anual average") ?></strong> <em><?php echo $anual_average ?></em>
    </div>
<?php endif ?>
</div>
<?php endif; ?>