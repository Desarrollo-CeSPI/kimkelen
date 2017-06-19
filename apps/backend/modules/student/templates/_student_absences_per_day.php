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
<h2> <?php echo ('Inasistencias por dia:');?> </h2>

<?php $student_career_school_years = $student->getStudentCareerSchoolYearsAscending();?>
<?php foreach ($student_career_school_years as $student_career_school_year): ?>

<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_total_absences">
  <div>
    <div>
        <h2><?php echo __("School year %%school_year%%", array("%%school_year%%" => $student_career_school_year->getCareerSchoolYear()->getSchoolYear())) ?></h2>
    </div>
      
    <?php if ($student_career_school_year->isAbsenceForPeriod()):?>
        <?php $course_subject_students_quaterly = $student->getCourseSubjectStudentsForCourseType(CourseType::QUATERLY) ?>
        <?php if ($course_subject_students_quaterly):?>
          <?php $periods = CareerSchoolYearPeriodPeer::retrieveByCourseTypeAndCareerSchoolYear(CourseType::QUATERLY,$student_career_school_year->getCareerSchoolYear())?>
          <?php include_partial('absence_day_table', array('student_career_school_year' => $student_career_school_year, 'student' => $student, 'periods' => $periods));?>
        <?php endif?>

        <?php $course_subject_students_trimester = $student->getCourseSubjectStudentsForCourseType(CourseType::TRIMESTER) ?>

        <?php if ($course_subject_students_trimester):?>
          <?php $periods = CareerSchoolYearPeriodPeer::retrieveByCourseTypeAndCareerSchoolYear(CourseType::TRIMESTER,$student_career_school_year->getCareerSchoolYear())?>
          <?php include_partial('absence_day_table', array('student_career_school_year' => $student_career_school_year, 'student' => $student, 'periods' => $periods));?>
        <?php endif?>

        <?php $course_subject_students_bimester = $student->getCourseSubjectStudentsForCourseType(CourseType::BIMESTER) ?>

        <?php if ($course_subject_students_bimester):?>
          <?php $periods = CareerSchoolYearPeriodPeer::retrieveByCourseTypeAndCareerSchoolYear(CourseType::BIMESTER,$student_career_school_year->getCareerSchoolYear())?>
          <?php include_partial('absence_day_table', array('student_career_school_year' => $student_career_school_year, 'student' => $student, 'periods' => $periods));?>
        <?php endif?>
    <?php else:?>
        <?php include_partial('absence_day_table_without_periods', array('student_career_school_year' => $student_career_school_year, 'student' => $student));?>
    <?php endif;?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<?php endforeach;?>