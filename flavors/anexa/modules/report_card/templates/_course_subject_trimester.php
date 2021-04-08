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
<?php $max_marks = 0 ?>
<?php foreach ($course_subject_students as $course_subject_student): ?>
	<?php $max_marks = ($course_subject_student->getCourseSubject()->countMarks() > $max_marks) ? $course_subject_student->getCourseSubject()->countMarks() : $max_marks ?>
<?php endforeach; ?>

<div class="title"><?php echo __('Marks'); ?></div>
<table class="gridtable">
	<tr>
		<?php if (!is_null($course_subject_students)): ?>
			<th class="th-subject-name"><?php echo __('Áreas-Materias') ?></th>
			<?php $max_marks = 0 ?>
			<?php foreach ($course_subject_students as $course_subject_student): ?>
				<?php $max_marks = ($course_subject_student->getCourseSubject()->countMarks() > $max_marks) ? $course_subject_student->getCourseSubject()->countMarks() : $max_marks ?>
			<?php endforeach; ?>

			<?php for ($mark_number = 1; $mark_number <= $max_marks; $mark_number++): ?>
				<th><?php echo __($mark_number . '°T') ?></th>
			<?php endfor; ?>
			<th><?php echo __('Prom.') ?></th>
			<th><?php echo __('PEEE dic.') ?></th>
			<th><?php echo __('PEEE feb.') ?></th>
			<th><?php echo __('Prom.Def.') ?></th>
	</tr>
	<?php foreach ($course_subject_students as $course_subject_student): ?>
		<tr>

			<?php if (!($course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubject()->getIsOption())): ?>
				<td class='subject_name'><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getName() ?></td>

				<?php for ($mark_number = 1; $mark_number <= $max_marks; $mark_number++): ?>
					<td><?php echo $course_subject_student->getMarkForIsClosed($mark_number) ?></td>
				<?php endfor; ?>
				<?php $course_result = $course_subject_student->getCourseResult() ?>
				<?php if (!$course_subject_student->hasSomeMarkFree()): ?>
					<td><?php echo ($course_result) ? $course_result->getResultStr() : '' ?></td>
				<?php else: ?>
					<td></td>
				<?php endif; ?>
				<td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(1)) ? $course_subject_student_examination->getMarkStrByConfig() : '' ?></td>
				<td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2)) ? $course_subject_student_examination->getMarkStrByConfig() : '' ?></td>
			  <td> 
                              <?php if( !is_null($course_result) && $course_result->getCourseSubjectStudent()->getIsNotAverageable() && ! is_null($course_result->getCourseSubjectStudent()->getNotAverageableCalification())): ?>
                                <?php if($course_result->getCourseSubjectStudent()->getNotAverageableCalification() == NotAverageableCalificationType::APPROVED): ?>
                                  <?php echo __("Promovido"); ?>
                                <?php else: ?>  
                                    <?php echo __("Trayectoria en curso"); ?>
                                <?php endif; ?>

                              <?php else: ?> 
                              <?php echo $student->getPromDef($course_result) ?>
                              <?php endif ?>
                          </td>
			<?php endif; ?>

		</tr>


	<?php endforeach; ?>

	<?php endif; ?>
</table>
