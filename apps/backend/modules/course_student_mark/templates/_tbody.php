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

<?php $i = 0;?>
<?php foreach ($course_subject->getCourseSubjectStudents() as $course_subject_student): ?>
  <?php $i++?>
  <?php $course_result = $course_subject_student->getCourseResult(); ?>
  <tr>
    <td><?php echo $i?></td>

    <td><?php echo $course_subject_student->getStudent()?></td>

    <?php foreach ($course_subject_student->getCourseSubjectStudentMarks() as $key=>$cssm): ?>
      <td align="center"><?php echo ((!$cssm->getMark())? __('free'): $cssm->getMark() ?  $cssm : ''); ?></td>
    <?php endforeach; ?>

    <td align="center"><?php echo ($final_period)? $course_subject_student->getMarksAverage() : '' ?></td>
    <td align="center"><?php echo $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(1); ?></td>
    <td align="center"><?php echo $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2); ?></td>
    <td align="center"><?php echo $course_subject_student->getStudentRepprovedCourseSubjectStrings()?></td>
    <td align="center"><?php echo ($course_subject_student->getStudentApprovedCareerSubject()) ? $course_subject_student->getFinalMark() : "" ?></td>
    <td></td>
  </tr>
<?php endforeach ?>