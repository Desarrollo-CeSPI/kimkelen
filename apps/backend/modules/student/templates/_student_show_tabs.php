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
<?php if (!($sf_user->isTeacher())): ?>
  <a class="tab tab-selected" href="#student_person" onclick="jQuery('fieldset').hide(); jQuery(jQuery(this).attr('href')).show(); jQuery('.tab').removeClass('tab-selected'); jQuery(this).addClass('tab-selected'); return false;">Datos personales</a>
  <?php $class = "tab" ?>
<?php else: ?>
  <?php $class = "tab tab-selected" ?>
<?php endif; ?>
<?php //if (!is_null($student->getCurrentStudentCareerSchoolYear())): ?>
  <a class="<?php echo $class ?>" href="#student_current_courses" onclick="jQuery('fieldset').hide(); jQuery(jQuery(this).attr('href')).show(); jQuery('.tab').removeClass('tab-selected'); jQuery(this).addClass('tab-selected'); return false;">Historial de cursadas</a>
<?php //endif ?>
<?php if ($student->hasAttendancesPerDay()): ?>
  <a class="tab" href="#student_absences_per_day" onclick="jQuery('fieldset').hide(); jQuery(jQuery(this).attr('href')).show(); jQuery('.tab').removeClass('tab-selected'); jQuery(this).addClass('tab-selected'); return false;">Historial de inasistencias por dia</a>
<?php endif ?>
<?php if ($student->hasAttendancesPerSubject()): ?>
  <a class="tab" href="#student_absences_per_subject" onclick="jQuery('fieldset').hide(); jQuery(jQuery(this).attr('href')).show(); jQuery('.tab').removeClass('tab-selected'); jQuery(this).addClass('tab-selected'); return false;">Historial de inasistencias por materia</a>
<?php endif ?>

<?php foreach ($student->getCareerStudents() as $career_student): ?>
  <?php if ($student->countStudentApprovedCareerSubjects()): ?>
    <a class="tab" href="#student_history_<?php echo $career_student->getId() ?>" onclick="jQuery('fieldset').hide(); jQuery(jQuery(this).attr('href')).show(); jQuery('.tab').removeClass('tab-selected'); jQuery(this).addClass('tab-selected'); return false;">Historia Académica <?php echo $career_student->getCareer() ?></a>
  <?php endif ?>
<?php endforeach ?>

<?php if (!($sf_user->isTeacher())): ?>
  <fieldset id="student_person">
    <?php echo get_partial('student/person', array('type' => 'list', 'student' => $student)) ?>
    <?php echo get_partial('student/address', array('type' => 'list', 'student' => $student)) ?>
    <?php echo get_partial('student/social_card', array('type' => 'list', 'student' => $student)); ?>
  </fieldset>
<?php endif; ?>

<fieldset id="student_current_courses">
  <?php foreach ($student->getStudentCareerSchoolYearsAscending() as $csy): ?>
    <?php echo get_partial('student/history_school_year', array('type' => 'list', 'student_career_school_year' => $csy, 'back_url' => '@student_show')) ?>
  <?php endforeach ?>
</fieldset>

<?php $student_career_school_years = $student->getCurrentStudentCareerSchoolYears()?>
<?php foreach ($student_career_school_years as $student_career_school_year):?>
  <?php if ($student->hasAttendancesPerDay()): ?>
    <fieldset id="student_absences_per_day">
      <?php echo get_partial('student/student_absences_per_day', array('type' => 'list', 'student' => $student, 'back_url' => '@student_show', 'student_career_school_year' => $student_career_school_year, 'reincorporations' => $student->getStudentReincorporationsPerDay())) ?>
    </fieldset>
  <?php endif?>
  <?php if ($student->hasAttendancesPerSubject()):?>
    <fieldset id="student_absences_per_subject">
      <?php echo get_partial('student/student_absences_per_subject', array('type' => 'list', 'student' => $student, 'back_url' => '@student_show', 'student_career_school_year' => $student_career_school_year, 'reincorporations' => $student->getStudentReincorporationsPerSubject())) ?>
    </fieldset>
  <?php endif?>
<?php endforeach ?>


<?php foreach ($student->getCareerStudents() as $career_student): ?>
  <?php if ($student->countStudentApprovedCareerSubjects()) : ?>
    <fieldset id="student_history_<?php echo $career_student->getId() ?>">
      <?php include_component('student', 'component_analytical_table', array('career_student' => $career_student)) ?>
    </fieldset>
  <?php endif ?>
<?php endforeach ?>


<script type="text/javascript">
  jQuery('fieldset:gt(0)').hide();
</script>
