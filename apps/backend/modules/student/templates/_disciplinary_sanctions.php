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
<?php use_helper('I18N') ?>
<div class="student_year"><?php echo __('Disciplinary sanctions') ?></div>
<div style="margin-left:5px">
  <?php if ($current_student_career_school_year = $student->getCurrentStudentCareerSchoolYear()): ?>
  <?php $max_disciplinary_sanctions= $current_student_career_school_year->getCareerSchoolYear()->getSubjectConfiguration()->getMaxDisciplinarySanctions() ?>
  <?php $student_disciplinary_sanctions = $student->countStudentDisciplinarySanctionsForSchoolYear() ?>

  <div class=<?php ($max_disciplinary_sanctions <= $student_disciplinary_sanctions)? "many_disciplinary_sanctions":''?>>
    <div><?php echo __('Total accumulated') . ": " . $student_disciplinary_sanctions; ?> </div>
    <?php if ($max_disciplinary_sanctions): ?>
      <div><?php echo __('Total allowed') . ": " . $max_disciplinary_sanctions; ?></div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
