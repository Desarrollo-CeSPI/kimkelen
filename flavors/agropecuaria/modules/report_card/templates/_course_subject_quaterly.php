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

<?php $is_repproved = $student_career_school_year->isRepproved() ?>
<div class="title"><?php echo ($has_attendance_for_subject) ? __('Marks for subject') : __('Marks for day'); ?></div>
<table class="gridtable">
  <tr>
    <?php include_partial('th_quaterly_tabular', array('has_attendance_for_subject' => $has_attendance_for_subject, 'course_subject_students' => $course_subject_students)) ?>

  </tr>
  <?php foreach ($course_subject_students as $course_subject_student): ?>
    <tr>

      <td class='subject_name'>
        <?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getName() ?></td>

      <?php # este for imprime las notas?>
      <?php for ($mark_number = 1; $mark_number <= $course_subject_student->getCourseSubject()->countMarks(); $mark_number++): ?>
        <?php if ($course_subject_student->getCourseSubject()->getCourseType() == CourseType::BIMESTER): ?>
          <?php $configs = $course_subject_student->getCourseSubject()->getCourseSubjectConfigurations(); ?>
          <?php $config = array_shift($configs); ?>
          <?php if ($config && $config->parentIsFirst()): ?>
            <td><?php echo $course_subject_student->getMarkForIsClose($mark_number) ?></td>
            <td>--</td>
          <?php else: ?>
            <td>--</td>
            <td><?php echo $course_subject_student->getMarkForIsClose($mark_number) ?></td>
          <?php endif; ?>

        <?php else: ?>
          <td><?php echo $course_subject_student->getMarkForIsClose($mark_number) ?></td>
        <?php endif; ?>
      <?php endfor; ?>



      <?php # Resultdo final del curso?>
      <td><?php echo ($course_result = $course_subject_student->getCourseResult()) ? $course_result->getResultStr() : '' ?></td>
      <?php # Diciembre?>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(1)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>
      <?php # Marzo?>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>

      <td>
        <?php # Repiitio?>
        <?php if ($is_repproved): ?>
          <?php echo $course_subject_student->getFinalAvg() ?>
        <?php else: ?>
          <?php echo $student->getPromDef($course_result) ?>
        <?php endif ?>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php $course_types_included = array(CourseType::QUATERLY, CourseType::BIMESTER); ?>
  <tr>
    <td class='partial_average'><?php echo __('Average') ?></td>
    <td><?php echo $student_career_school_year->getAverageFor(1, $course_types_included);?></td>
    <td><?php echo $student_career_school_year->getAverageFor(2, $course_types_included); ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td class="td_average"><?php echo $student_career_school_year->getAnualAverage() ?></td>
  </tr>
</table>