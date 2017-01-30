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
<?php use_stylesheet('/frontend/css/main.css', 'first') ?>
 <div class="row"> 
 	<div class="col-md-1"></div>
 	<div class="col-md-10 container-sombra-exterior container-attendance">

 		<div class="student-info title-box box-attendance">	
			<span class="glyphicon glyphicon-file icon-title large violet" aria-hidden="true"></span>
			<span class="title-sanctions"><?php echo __('Attendance'); ?> |</span>
			<span class=""><?php echo $student . ' - ' . __('school year') . ' '. $school_year->getYear()?></span>
		</div>
 		<?php if($student->hasAttendancesPerDay()): ?>

 			<?php include_partial('attendance_per_day', array('division'=>$division,'student'=>$student)); ?>
 			
 		<?php else: ?>

 			<?php include_partial('attendance_per_subject', array('student_career_school_year'=>$student_career_school_year,'student' => $student)); ?>

	 	<?php endif; ?>

 	</div>
 	<div class="col-md-1"></div>
 	<div class="col-md-12 container-buttons">
		<div class="col-md-1"></div>
		<div class="col-md-10">
			<div class="col-md-6">
				<div class="button button_1 go-back">
					<?php echo link_to(__('Go back'), $link);?>
				</div>	
			</div>
			<div class="col-md-6">
				<div class="button button_2 report">
					<?php echo link_to(__('Attendance report'), 'student_attendance/showReport?student_id=' . $student->getId());?>
				</div>
			</div>
		</div>
		<div class="col-md-1"></div>	
	</div>
 </div>