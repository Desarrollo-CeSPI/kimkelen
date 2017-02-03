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
	<?php if(is_null($student_career_school_year)):?>
		<div class="info-attendance"><?php echo __('No se registraron inasistencias para este alumno.'); ?></div>
	<?php else: ?>
		<?php $course_subjects_student = CourseSubjectStudentPeer:: retrieveByCareerSchoolYearAndStudent($student_career_school_year->getCareerSchoolYear(),$student); ?>
 		<div class="col-md-1"></div>
 		<div class="col-md-10">
 			<div class="table-responsive"> 
		      	<table class="table table-condensed">
		          	<tbody>
		                <tr class="success">
		                  	<th colspan="2"><?php echo __('Course') ?></th>  	
		                    <th colspan="2"><?php echo __('Attendance') .' / ' .__('Total allowed') ?></th>
		                </tr>
		                <?php $global = 0; ?> 
		                <?php foreach ($course_subjects_student as $css): ?>
		                <tr>
		                    <td colspan="2"> <?php echo $css->getCourseSubject() ?></td>
		                    <td colspan="2">
		                <?php  

			                  $course_subject_configurations = CourseSubjectConfigurationPeer::retrieveBySubject($css->getCourseSubject());
			                  $total = 0;
			                  foreach ($course_subject_configurations as $c) {
				                  	$total += $c->getMaxAbsence();
			                  }
			                  $value = StudentAttendancePeer::doCountAbsenceByCourseSubjectAndStudent($css->getCourseSubject(), $student);
			                  $global += $value ;
			                  echo $value .'/' . $total . 'hs.'; 
	 
			                ?>
				                </td>
				            </tr>
			                <?php endforeach; ?>
			         	</tbody>          
			        </table>
				</div>
				<div class="pull-right">
			       	<b>Total: </b> <?php echo $global .'hs.'?>
			    </div>
 			</div>
 			<div class="col-md-1"></div>
	<?php endif; ?>
	
