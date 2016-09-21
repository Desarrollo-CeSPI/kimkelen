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
<div class="title"><?php echo __('Marks') ?></div>
<table class="gridtable">
  <?php if (count($course_subject_students_first_q = $student->getCourseSubjectStudentsForBimesterFirstQuaterly($student_career_school_year)) > 0): ?>
    <tr><?php include_partial('th_bimester_tabular', array('division' => $division, 'number' => '1')) ?></tr>
  <?php endif; ?>

  <?php foreach ($course_subject_students_first_q as $course_subject_student): ?>
    <tr>
      <td class='subject_name'><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getName() ?></td>
      <td><?php echo $course_subject_student->getMarkForIsClosed(1) ?></td>
      <td><?php echo $course_subject_student->getMarkForIsClosed(2) ?></td>
      <td><?php echo $course_subject_student->getMarkForIsClosed(3) ?></td>

      <td><?php echo ($course_result = $course_subject_student->getCourseResult()) ? $course_result->getResultStr() : '' ?></td>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(1)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>
      <?php if (!is_null($student_repproved_course_subject = $course_subject_student->repprovedCourseSubjectHasBeenApproved())): ?>
        <td><?php echo ($student_repproved_course_subject->getLastMarkStr()) ?></td>
      <?php else: ?>
        <td><?php #echo $course_subject_student->getLastStudentDisapprovedCourseSubject()  ?></td>
      <?php endif; ?>

      <td> <?php echo $student->getPromDef($course_result) ?></td>

    </tr>
  <?php endforeach; ?>
</table>


<table class="gridtable">
  <?php if (count($course_subject_students_second_q = $student->getCourseSubjectStudentsForBimesterSecondQuaterly($student_career_school_year)) > 0): ?>
    <tr>
      <?php include_partial('th_bimester_tabular', array('division' => $division, 'number' => '2')) ?>
    </tr>
  <?php endif; ?>
  <?php foreach ($course_subject_students_second_q as $course_subject_student): ?>
    <tr>
      <td class='subject_name'><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getName() ?></td>
      <td><?php echo $course_subject_student->getMarkForIsClosed(1) ?></td>
      <td><?php echo $course_subject_student->getMarkForIsClosed(2) ?></td>
      <td><?php echo $course_subject_student->getMarkForIsClosed(3) ?></td>
      <td><?php echo ($course_result = $course_subject_student->getCourseResult()) ? $course_result->getResultStr() : '' ?></td>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(1)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>
      <?php if (!is_null($student_repproved_course_subject = $course_subject_student->repprovedCourseSubjectHasBeenApproved())): ?>
        <td><?php echo ($student_repproved_course_subject->getLastMarkStr()) ?></td>
      <?php else: ?>
        <td><?php #echo $course_subject_student->getLastStudentDisapprovedCourseSubject()  ?></td>
      <?php endif; ?>

      <td> <?php echo $student->getPromDef($course_result) ?></td>

    </tr>
  <?php endforeach; ?>
</table>