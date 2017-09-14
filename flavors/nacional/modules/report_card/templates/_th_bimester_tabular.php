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

<?php if (isset($course_subject_students) && !is_null($course_subject_students)): ?>
  <th class='th-subject-name'><?php echo __('Áreas-Materias') ?> - <?php echo $number ?>°C</th>
  <?php $max_marks = 0 ?>
  <?php foreach ($course_subject_students as $course_subject_student): ?>
    <?php $max_marks = ($course_subject_student->getCourseSubject()->countMarks() > $max_marks) ? $course_subject_student->getCourseSubject()->countMarks() : $max_marks ?>
  <?php endforeach; ?>
  <?php for ($mark_number = 1; $mark_number <= $max_marks; $mark_number++): ?>
    <?php if($mark_number == 3):?>
      <th><?php echo __('PF') ?></th>
      <?php else:?>
      <th><?php echo __($mark_number . '°B') ?></th>
      <?php endif;?>
  <?php endfor; ?>

  <th><?php echo __('Prom.') ?></th>
  <th><?php echo __('Ex.Reg.') ?></th>
  <th><?php echo __('Ex.Comp.') ?></th>
  <th><?php echo __('Ex.Prev.') ?></th>
  <th><?php echo __('Prom.Def.') ?></th>

  <?php if (!$division->hasAttendanceForDay()): ?>

    <th><?php echo __('Inasist. 1°B') ?></th>
    <th><?php echo __('Inasist. 2°B') ?></th>
  <?php endif; ?>
<?php endif; ?>
