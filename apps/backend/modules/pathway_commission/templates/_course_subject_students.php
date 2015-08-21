<?php /*
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

<?php $career = $course_subject->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getCareer(); ?>

<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_marks_count">

  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_marks_course_minimun_mark">
  <div>
    <label for="course_minimun_mark"> <?php echo __('Course minimun mark'); ?> </label>
	  <?php $evaluator_instance = SchoolBehaviourFactory::getEvaluatorInstance() ?>
    <?php echo  $evaluator_instance::PATHWAY_PROMOTION_NOTE ?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>

<div class="sf_admin_form_row sf_admin_Text">
<div>
	<label> <?php echo __('Average'); ?> </label>
	<?php echo __('Pathway mark is averaged with course subject average marks.') ?>
</div>
<div style="margin-top: 1px; clear: both;"></div>
</div>

<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_students">
  <div>
    <label for="students"> <?php echo __('Students'); ?> </label>
    <table>
      <thead>
        <tr>
          <th><?php echo __('File number'); ?></th>
          <th><?php echo __('Student'); ?></th>

          <th><?php echo __('Mark'); ?></th>

          <th><?php echo __('Average'); ?></th>
          <th><?php echo __('Result'); ?></th>

        </tr>
      </thead>
      <tbody>
        <?php foreach ($course_subject->getCourseSubjectStudentPathways() as $course_subject_student): ?>
	        <?php $course_marks_avg =  SchoolBehaviourFactory::getEvaluatorInstance()->getMarksAverage($course_subject_student->getRelatedCourseSubjectStudent()); ?>
          <?php if ($course_subject_student->getMark() >= $evaluator_instance::PATHWAY_PROMOTION_NOTE): ?>
		        <?php $course_result = __('Approved'); ?>
		        <?php $final_mark = bcdiv($course_subject_student->getMark() + $course_marks_avg, 2, 2); ?>
	        <?php else: ?>
	          <?php $course_result = __('Dissaproved'); ?>
		        <?php $final_mark = $course_subject_student->getMark(); ?>
	        <?php endif; ?>
          <tr>
            <td><?php echo $course_subject_student->getStudent()->getFileNumber($career) ?></td>
            <td><?php echo $course_subject_student->getStudent() ?></td>
            <td><?php echo $course_subject_student->getMark(); ?></td>
            <td><?php echo $final_mark ?></td>
            <td><?php echo $course_result ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>