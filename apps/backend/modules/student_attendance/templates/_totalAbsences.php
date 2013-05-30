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
<?php if (!is_null($course_subject_id)): ?>
  <?php $clazz = $student->getFreeClass($period, isset($course_subject_id) ? $course_subject_id : null) ?>
  <div class="<?php echo $clazz ?> student_attendance_period">
    <div>
      <?php echo __('Absences:  %total%', array(
          '%total%' => round($student->getTotalAbsences($career_school_year_id, $period, $course_subject_id, true), 2)))
      ?>
    </div>
    <div>
      <?php echo __('Remaining: %total%', array(
          '%total%' => round($student->getRemainingAbsenceFor($period, $course_subject_id, true), 2)))
      ?>
    </div>
    <?php if ($clazz == 'free'): ?>
      <div><?php echo __('Is free') ?></div>
    <?php endif ?>

  </div>

<?php else: ?>
<div class="student_attendance_period">
  <div>
    <?php echo __('Absences:  %total%', array(
        '%total%' => round($student->getAmountStudentAttendanceUntilDay( strtotime($day . '+ 7 day a go')), 2)))
    ?>
  </div>
</div>
<?php endif; ?>