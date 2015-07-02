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
<div class="title"><?php echo  __('Boletin de calificaciones'); ?></div>
<table class="gridtable">
  <tr>
    <?php include_partial('th_quaterly_tabular', array('has_attendance_for_subject' => $has_attendance_for_subject)) ?>

  </tr>
  <?php foreach ($course_subject_students as $course_subject_student): ?>
    <tr>
      <td class='subject_name'><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getName() ?></td>
      <?php if ($course_subject_student->getIsNotAverageable()): ?>
      <td><?php echo SchoolBehaviourFactory::getEvaluatorInstance()->getExemptString() ?></td>
      <td><?php echo SchoolBehaviourFactory::getEvaluatorInstance()->getExemptString() ?></td>
      <?php else: ?>
      <td><?php echo $course_subject_student->getMarkForIsClose(1) ?></td>
      <td><?php echo $course_subject_student->getMarkForIsClose(2) ?></td>
      <?php endif; ?>
      <td><?php echo ($course_result = $course_subject_student->getCourseResult()) ? $course_result->getResultStr() : '' ?></td>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(1)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>

      <td>
        <?php if ($is_repproved): ?>
          <?php echo $course_subject_student->getFinalAvg() ?>
        <?php else: ?>
          <?php echo!is_null($student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($course_subject_student)) ? $student_approved_career_subject->getMark() : ''; ?>
        <?php endif ?>
      </td>

      <?php if ($has_attendance_for_subject): ?>
        <?php foreach ($periods as $period): ?>
          <td>
            <?php echo $period->getIsClosed()
                ? round($student->getTotalAbsences($division->getCareerSchoolYearId(), $period, $course_subject_student->getCourseSubjectId(), true), 2)
                : '&nbsp'
            ?>
          </td>
        <?php endforeach; ?>
      <?php endif; ?>

    </tr>
  <?php endforeach; ?>
  <tr>
    <td class='partial_average'><?php echo __('Average') ?></td>
    <?php foreach ($periods as $period): ?>
      <?php if ($period->getIsClosed()): ?>
        <td class="td_average"><?php echo SchoolBehaviourFactory::getEvaluatorInstance()->getPartialAverageForQuaterly($course_subject_students, (array_search($period, $periods) + 1)); ?></td>
        <td></td>
        <td></td>

      <?php else: ?>
        <td></td>
        <td></td>
        <td></td>

      <?php endif; ?>
    <?php endforeach; ?>
  </tr>
</table>