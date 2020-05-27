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
<div class="title"><?php echo __('Observations') ?></div>
<table class="gridtable">
  <?php if (count($course_subject_students_first_q = $student->getCourseSubjectStudentsForBimesterFirstQuaterly($student_career_school_year)) > 0): ?>
    <tr>
       <th class='th-subject-name'><?php echo __('Bimestrales')?> - 1°C</th>
       <th><?php echo __('1°B') ?></th>
       <th><?php echo __('2°B') ?></th>
    </tr>
  <?php endif; ?>

  <?php foreach ($course_subject_students_first_q as $course_subject_student): ?>
    <tr>
      <td class='subject_name'><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getName() ?></td>
      <td><?php echo $course_subject_student->getObservationForIsClosed(1) ?></td>
      <td><?php echo $course_subject_student->getObservationForIsClosed(2) ?></td>
    </tr>
  <?php endforeach; ?>
</table>


<table class="gridtable">
  <?php if (count($course_subject_students_second_q = $student->getCourseSubjectStudentsForBimesterSecondQuaterly($student_career_school_year)) > 0): ?>
    <tr>
      <th class='th-subject-name'><?php echo __('Bimestrales')?> - 2°C</th>
      <th><?php echo __('1°B') ?></th>
      <th><?php echo __('2°B') ?></th>
    </tr>
  <?php endif; ?>
  <?php foreach ($course_subject_students_second_q as $course_subject_student): ?>
    <tr>
      <td class='subject_name'><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getName() ?></td>
      <td><?php echo $course_subject_student->getObservationForIsClosed(1) ?></td>
      <td><?php echo $course_subject_student->getObservationForIsClosed(2) ?></td>
    </tr>
  <?php endforeach; ?>
</table>
