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

 <div class="row"> 
 	<div class="col-md-1"></div>
 	<div class="col-md-10 container-sombra-exterior container-attendance">
 		<div class="box-title">		
			<span class="title-sanctions"><?php echo __('Attendance'); ?> |</span>
			<span class=""><?php echo $student . ' - ' . __('school year') . ' '. $school_year->getYear()?></span>
		</div>
 		<?php if(! $student->hasAttendancesPerSubject()): ?>

 			<?php include_partial('attendance_per_day', array('division'=>$division,'student'=>$student)); ?>
 			
 		<?php else: ?>

 			<?php include_partial('attendance_per_subject', array('student_career_school_year'=>$student_career_school_year,'student' => $student)); ?>

	 	<?php endif; ?>
	<div class="col-md-12 container-buttons">
		<?php echo link_to(__('Go back'), $link, array("class"=> "button_1"));?>
		<?php echo link_to(__('Show report'), 'student_attendance/showReport?student_id=' . $student->getId(), array("class"=> "button_2"));?>		
	</div>
 	</div>
 	<div class="col-md-1"></div>
 </div>
