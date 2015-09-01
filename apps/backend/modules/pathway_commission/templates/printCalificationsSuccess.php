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
<?php use_stylesheet('report-card.css', 'first', array('media' => 'screen')) ?>
<?php use_stylesheet('print-report-card.css', 'last', array('media' => 'print')) ?>
<?php use_stylesheet('main.css', '', array('media' => 'all')) ?>

<div class="non-printable">
	<span><a href="<?php echo url_for('pathway_commission/index') ?>"><?php echo __('Go back') ?></a></span>
	<span><a href="#" onclick="window.print(); return false;"><?php echo __('Print') ?></a></span>
	<form action="<?php echo url_for('@print_table') ?>" method="post" target="_blank" id="exportation_form">
		<input type="hidden" id="send_data" name="send_data" />
	</form>
</div>

<div class="report-wrapper">
	<?php foreach ($course_subjects as $course_subject): ?>
		<?php $career = $course_subject->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getCareer(); ?>
		<?php $course = $course_subject->getCourse(); ?>
		<?php $final_period = $course_subject->isFinalPeriod(); ?>
		<?php $configuration = $course_subject->getCareerSubjectSchoolYear()->getConfiguration() ?>

		<div class="report-header">
			<div class="logo"><?php echo image_tag("kimkelen_logo.png", array('absolute' => true)) ?></div>
			<div class="header_row">
				<h2><?php echo __('Print califications'); ?></h2>

				<div class="title2"><?php echo __('Course') ?>: </div>
				<div class="row-content"><?php echo $course . ' (' .  __('Pathway') . ')' ?></div>
			</div>
			<div class="header_row">
				<div class="title2"><?php echo __('Course minimun mark'); ?>:
					<?php $evaluator_instance = SchoolBehaviourFactory::getEvaluatorInstance() ?></div>
					<div class="row-content"><?php echo  $evaluator_instance::PATHWAY_PROMOTION_NOTE ?></div>
			</div>
			<div class="header_row">
				<div class="title2"><?php echo __('Average'); ?>:</div>
				<div class="row-content"><?php echo __('Pathway mark is averaged with course subject average marks.') ?></div>
			</div>
		</div>
			<table width="100%" class="gridtable_bordered">
				<thead>
				<tr>
					<th><?php echo __('Student'); ?></th>
					<th><?php echo __('Mark'); ?></th>
					<th><?php echo __('Average'); ?></th>
					<th><?php echo __('Result'); ?></th>
				</tr>
				</thead>

				<tbody class="print_body">
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
						<td style="text-align: left"><?php echo $course_subject_student->getStudent() ?></td>
						<td><?php echo $course_subject_student->getMark(); ?></td>
						<td><?php echo $final_mark ?></td>
						<td><?php echo $course_result ?></td>
					</tr>

				<?php endforeach; ?>
				</tbody>
			</table>
	<?php endforeach; ?>
</div>