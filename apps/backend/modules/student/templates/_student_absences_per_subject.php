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
<div>
  <h2> <?php echo __('Absence per subject:'); ?> </h2>
</div>
<?php $student_career_school_years = $student->getStudentCareerSchoolYearsAscending();?>
<?php foreach ($student_career_school_years as $student_career_school_year): ?>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_total_absences">
  <div>
      <h2><?php echo __("School year %%school_year%%", array("%%school_year%%" => $student_career_school_year->getCareerSchoolYear()->getSchoolYear())) ?></h2>
      <?php include_partial('absence_subject_table', array('student_career_school_year' => $student_career_school_year, 'student' => $student)); ?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<?php endforeach; ?>