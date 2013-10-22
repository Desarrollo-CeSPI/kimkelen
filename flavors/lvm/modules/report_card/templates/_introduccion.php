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
<div class="title"><?php echo __('Trimestrales') ?></div>
<table class="gridtable">
  <tr>
    <th class="th-subject-name"><?php echo __('Intro. a la Problemática de:') ?></th>
    <th><?php echo __('1°T') ?></th>
    <th><?php echo __('Ex.R.') ?></th>
    <th><?php echo __('Ex.C.') ?></th>
    <th><?php echo __('Ex.P.') ?></th>
    <th><?php echo __('Inasistencias') ?></th>
    <th><?php echo __('Prom.Def.') ?></th>

  </tr>
  <?php $avg_mark = 0 ?>
  <?php $count_approved = 0 ?>
  <?php $approved = true; ?>
  <?php foreach ($course_subject_students as $course_subject_student): ?>
    <?php $course_result = $course_subject_student->getCourseResult() ?>
    <?php if ($course_result): ?>
      <?php $approved = ($approved && $course_result->isApproved()) ?>
      <?php if ($approved): ?>
  <?php $count_approved++; ?>
        <?php $avg_mark += $course_result->getFinalMark() ?>
      <?php endif ?>
    <?php endif; ?>
  <?php endforeach ?>


  <?php $first = true; ?>

  <?php foreach ($course_subject_students as $course_subject_student): ?>
    <?php $course_result = $course_subject_student->getCourseResult() ?>
    <tr>
      <td><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getFantasyName() ?></td>
      <td><?php echo $mark = $course_subject_student->getMarkFor(1) ?></td>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(1)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>
      <td></td>
      <td><?php echo round($student->getTotalAbsences($division->getCareerSchoolYearId(), null, $course_subject_student->getCourseSubjectId(), true), 2) ?></td>
      <?php if ($first): ?>
        <?php $first = false; ?>
        <td rowspan="3">
          <?php if ($count_approved == 3): ?>
            <?php echo sprintf('%.4s', ($avg_mark / 3)); ?>
          <?php endif ?>
        </td>
      <?php endif ?>
    </tr>

  <?php endforeach; ?>
</table>