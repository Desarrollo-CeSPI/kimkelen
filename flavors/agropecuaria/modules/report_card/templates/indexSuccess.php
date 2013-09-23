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
<?php use_stylesheet('/css/report-card.css') ?>
<?php use_stylesheet('/css/print-report-card.css', '', array('media' => 'print')) ?>
<?php use_helper('Asset', 'I18N') ?>

<div class="non-printable">
  <div><a href="<?php echo url_for('@export_report_cards?sf_format=pdf') ?>"><?php echo __('Export') ?></a></div>
  <div><a href="<?php echo url_for($back_url) ?>"><?php echo __('Go back') ?></a></div>
</div>

<?php foreach ($students as $student): ?>
  <?php $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $division->getCareerSchoolYear()) ?>
  <div class="report-wrapper">
    <?php include_partial('header', array('student' => $student, 'division' => $division, 'career_id' => $career_id, 'school_year' => $student_career_school_year->getSchoolYear(), 'student_career' => CareerStudentPeer::retrieveByCareerAndStudent($career_id, $student->getId()))); ?>
    <div class="report-content">

      <?php $periods = CareerSchoolYearPeriodPeer::getQuaterlyPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>

      <?php $school_year = $division->getSchoolYear(); ?>
      <?php $course_subject_students = $student->getCourseSubjectStudentsForSchoolYear($school_year); ?>

      <?php include_partial('course_subject_quaterly', array('student' => $student, 'course_subject_students' => $course_subject_students, 'periods' => $periods, 'has_attendance_for_subject' => false, 'student_career_school_year' => $student_career_school_year)) ?>
      <div class="footer" style="width: 100%">
        <?php include_partial('footer', array('student' => $student, 'division' => $division)); ?>
      </div>
    </div>
    <hr class="hr_break">
    <div class="report-content">
      <?php include_partial('admonition_details', array('student' => $student, 'division' => $division)); ?>
      <hr class="hr_break">
      <?php $examination_repproveds = $student->getStudentRepprovedCourseSubjectForSchoolYear(SchoolYearPeer::retrieveLastYearSchoolYear($division->getCareerSchoolYear()->getSchoolYear())) ?>
      <?php include_partial('career_subject_repproved_details', array('examination_repproveds' => $examination_repproveds)); ?>

      <?php include_partial('signature_boxes') ?>
      <div style="clear:both;"></div>
      <br>
      <div class="date"><?php echo __('Issue date') ?> <?php echo date('d/m/Y') ?></div>
    </div>
  </div>
  <div style="clear:both;"></div>
  <div style="page-break-before: always;"></div>
<?php endforeach; ?>