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
<?php use_helper('Javascript', 'Object', 'I18N', 'Asset') ?>
<?php use_stylesheet('/css/print-assistanceAndSanction.css', '', array('media' => 'print')) ?>
<?php use_stylesheet('/css/assistanceAndSanction.css') ?>

<div class="non-printable">
  <a href="#" onclick="window.print(); return false;"><?php echo __('Print') ?></a>
  <a href="<?php echo url_for('division') ?>"><?php echo __('Go back') ?></a>
</div>

<?php foreach ($students as $student): ?>
  <?php $student_career_school_years = $student->getCurrentStudentCareerSchoolYears(); ?>

  <?php foreach ($student_career_school_years as $student_career_school_year): ?>
    <?php $school_year = $student_career_school_year->getSchoolYear(); ?>
    <?php foreach ($student_career_school_year->getDivisions() as $division): ?>

      <div class="report-wrapper">
        <?php include_partial('admonition_details', array('student' => $student, 'division' => $division, 'student_career_school_year' => $student_career_school_year)); ?>

        <div style="clear:both"></div>

        <div style="page-break-before: always;"></div>
      <?php endforeach ?>
    <?php endforeach ?>
  <?php endforeach ?>
