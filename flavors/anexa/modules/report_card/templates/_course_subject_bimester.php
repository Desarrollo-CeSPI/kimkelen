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
<div class="title"><?php echo __('Marks') ?></div>
<table class="gridtable">
	<?php if (count($course_subject_students_first_q = $student->getCourseSubjectStudentsForBimesterFirstQuaterly($student_career_school_year)) > 0): ?>
		<tr>

			<?php if (isset($course_subject_students_first_q) && !is_null($course_subject_students_first_q)): ?>
				<th class='th-subject-name'><?php echo __('Áreas-Materias') ?> - 1°C</th>
				<?php $max_marks = 0 ?>
				<?php foreach ($course_subject_students_first_q as $course_subject_student): ?>
					<?php $max_marks = ($course_subject_student->getCourseSubject()->countMarks() > $max_marks) ? $course_subject_student->getCourseSubject()->countMarks() : $max_marks ?>
				<?php endforeach; ?>
				<?php for ($mark_number = 1; $mark_number <= $max_marks; $mark_number++): ?>
					<th><?php echo __($mark_number . '°B') ?></th>
				<?php endfor; ?>

				<th><?php echo __('Prom.') ?></th>
				<th><?php echo __('Ex.R.') ?></th>
				<th><?php echo __('Prom.Def.') ?></th>


			<?php endif; ?>

		</tr>
		<?php #Calculo el maximo de notas que va a tener el boletin en el PRIMER bimester?>
		<?php $max_marks_first_q = 0 ?>
		<?php foreach ($course_subject_students_first_q as $course_subject_student): ?>
			<?php $max_marks_first_q = ($course_subject_student->getCourseSubject()->countMarks() > $max_marks_first_q) ? $course_subject_student->getCourseSubject()->countMarks() : $max_marks_first_q ?>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php foreach ($course_subject_students_first_q as $course_subject_student): ?>
		<tr>
			<td class='subject_name'><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getName() ?></td>
			<?php #Imprimimos las notas del PRIMER bimester?>
			<?php for ($mark_number = 1; $mark_number <= $course_subject_student->getCourseSubject()->countMarks(); $mark_number++): ?>
				<td><?php echo $course_subject_student->getMarkForIsClose($mark_number) ?></td>
			<?php endfor; ?>
			<?php #Completo los casilleros extras que  tienen si mi materia tiene menos notas que  el maximo del bimestre?>
			<?php if ($max_marks_first_q > $course_subject_student->getCourseSubject()->countMarks()): ?>
				<?php $dif = ($max_marks_first_q - $course_subject_student->getCourseSubject()->countMarks()) ?>
				<?php for ($extra_td = 1; $extra_td <= $dif; $extra_td++): ?>
					<td></td>
				<?php endfor; ?>
			<?php endif; ?>

			<?php $course_result = $course_subject_student->getCourseResult() ?>
			<?php if (!$course_subject_student->hasSomeMarkFree()): ?>
				<td><?php echo ($course_result) ? $course_result->getResultStr() : '' ?></td>
			<?php else: ?>
				<td></td>
			<?php endif; ?>

			<td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>

			<td>
					<?php echo $student->getPromDef($course_result) ?>
			</td>
			<?php if (!$division->hasAttendanceForDay()): ?>
				<?php foreach ($periods[0] as $period): ?>
					<td>
						<?php
						echo $period->getIsClosed() ? round($student->getTotalAbsences($division->getCareerSchoolYearId(), $period, $course_subject_student->getCourseSubjectId(), true), 2) : '&nbsp'
						?>
					</td>
				<?php endforeach; ?>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>
</table>


<table class="gridtable">
	<?php if (count($course_subject_students_second_q = $student->getCourseSubjectStudentsForBimesterSecondQuaterly($student_career_school_year)) > 0): ?>
		<tr>

			<?php if (isset($course_subject_students_second_q) && !is_null($course_subject_students_second_q)): ?>
				<th class='th-subject-name'><?php echo __('Áreas-Materias') ?> - 2°C</th>
				<?php $max_marks = 0 ?>
				<?php foreach ($course_subject_students_second_q as $course_subject_student): ?>
					<?php $max_marks = ($course_subject_student->getCourseSubject()->countMarks() > $max_marks) ? $course_subject_student->getCourseSubject()->countMarks() : $max_marks ?>
				<?php endforeach; ?>
				<?php for ($mark_number = 1; $mark_number <= $max_marks; $mark_number++): ?>
					<th><?php echo __($mark_number . '°B') ?></th>
				<?php endfor; ?>

				<th><?php echo __('Prom.') ?></th>
				<th><?php echo __('Ex.R.') ?></th>
				<th><?php echo __('Prom.Def.') ?></th>

			<?php endif; ?>

		</tr>

		<?php #Calculo el maximo de notas que va a tener el boletin en el SEGUNDO bimester ?>
		<?php $max_marks_second_q = 0 ?>
		<?php foreach ($course_subject_students_second_q as $course_subject_student): ?>
			<?php $max_marks_second_q = ($course_subject_student->getCourseSubject()->countMarks() > $max_marks_second_q) ? $course_subject_student->getCourseSubject()->countMarks() : $max_marks_second_q ?>
		<?php endforeach; ?>

	<?php endif; ?>
	<?php foreach ($course_subject_students_second_q as $course_subject_student): ?>
		<tr>
			<td class='subject_name'><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getName() ?></td>
			<?php #Imprimimos las notas del SEGUNDO bimester ?>
			<?php for ($mark_number = 1; $mark_number <= $course_subject_student->getCourseSubject()->countMarks(); $mark_number++): ?>
				<td><?php echo $course_subject_student->getMarkForIsClose($mark_number) ?></td>
			<?php endfor; ?>
			<?php #Completo los casilleros extras que  tienen si mi materia tiene menos notas que  el maximo del bimestre?>
			<?php if ($max_marks_second_q > $course_subject_student->getCourseSubject()->countMarks()): ?>
				<?php $dif = ($max_marks_second_q - $course_subject_student->getCourseSubject()->countMarks()) ?>
				<?php for ($extra_td = 1; $extra_td <= $dif; $extra_td++): ?>
					<td></td>
				<?php endfor; ?>
			<?php endif; ?>


			<td><?php echo ($course_result = $course_subject_student->getCourseResult()) ? $course_result->getResultStr() : '' ?></td>
			<td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>
			<!-- <td><?php #echo $course_subject_student->getLastStudentDisapprovedCourseSubject() ?></td> -->

			<td>
					<?php echo $student->getPromDef($course_result) ?>
			</td>
			<?php if (!$division->hasAttendanceForDay()): ?>
				<?php foreach ($periods[1] as $period): ?>
					<td>
						<?php
						echo $period->getIsClosed() ? round($student->getTotalAbsences($division->getCareerSchoolYearId(), $period, $course_subject_student->getCourseSubjectId(), true), 2) : '&nbsp'
						?>
					</td>
				<?php endforeach; ?>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>
</table>
