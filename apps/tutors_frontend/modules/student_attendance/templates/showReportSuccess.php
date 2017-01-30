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
<?php use_stylesheet('/frontend/css/main.css', 'first') ?>
<div class="row">
	<div class="col-md-1"></div>
	<div class="col-md-10 container-sombra-exterior container-report-sanctions">
		<div class="title-report-details">
          	<span class="title-sanctions"><?php echo __('Attendance report') . ' |'; ?></span> 
          	<span><?php echo $student . ' - ' . __('school year') . ' '. $school_year->getYear() ?></span>
      	</div>

      	<?php if (count($absences) == 0): ?>
      		<span class=""><?php echo __('No se registraron inasistencias para este alumno.'); ?></span>
		<?php else: ?>
			<div class="table-responsive">
		        <table class="table table-condensed">
		          	<thead>
		            	<tr class="success">
		            	  	<th><?php echo __('Day') ?></th>
		              		<th><?php echo __('Absence') ?></th>
		              		<?php if ($student->hasAttendancesPerSubject()): ?>
		                	<th><?php echo __('Subject') ?></th>
		              		<?php endif; ?>
		              		<th><?php echo __('Is justified') ?></th>
		              		<th><?php echo __('Justification type') ?></th>
		              		<th><?php echo __('Description') ?></th>
		            	</tr>
		          	</thead>
		          	<tbody>
		          	<?php $justified = 0;?>
		          	<?php $unjustified = 0;?>
		            <?php foreach ($absences as $absence): ?>
		              	<tr>
		              		<?php if ($absence->getStudentAttendanceJustification()) $justified++; else $unjustified++; ?>
		                	<td><?php echo $absence->getFormattedDay(); ?></td>
		                	<td><?php echo $absence->getValueString() ?></td>
		                	<?php if ($student->hasAttendancesPerSubject()): ?>
		                	<td><?php echo ($course_subject = $absence->getCourseSubject()) ? $absence->getCourseSubject() : '-' ?></td>
		                	<?php endif; ?>
		                	<td><?php echo ($justification = $absence->getStudentAttendanceJustification()) ? 'Sí' : 'No' ?></td>
		                	<td><?php echo ($type = $absence->getStudentAttendanceJustification()) ? $absence->getStudentAttendanceJustification()->getJustificationType() : '-' ?></td>
		                	<td><?php echo ($justification = $absence->getStudentAttendanceJustification()) ? $absence->getStudentAttendanceJustification()->getObservation() : '-' ?></td>
		              	</tr>
		            <?php endforeach; ?>
		          	</tbody>
		          	<tfoot>
		          		<tr>
		          			<td></td>
		          			<td></td>
		          			<?php if ($student->hasAttendancesPerSubject()): ?>
		                	<td></td>
		                	<?php endif; ?>
		                	<td></td>
		                	<td></td>
		          			<td><span class="pull-right"><b><?php echo __('Total') .': '?> </b> <?php echo $justified + $unjustified; ?></span></td>
		          		</tr>
		          		<tr>
		          			<td></td>
		          			<td></td>
		          			<?php if ($student->hasAttendancesPerSubject()): ?>
		                	<td></td>
		                	<?php endif; ?>
		                	<td></td>
		                	<td></td>
		                	<td><span class="pull-right"><b><?php echo __('Unjustified').': '?></b><?php echo $unjustified;?></span></td>
		          		</tr>
		          		<tr>
		          			<td></td>
		          			<td></td>
		          			<?php if ($student->hasAttendancesPerSubject()): ?>
		                	<td></td>
		                	<?php endif; ?>
		                	<td></td>
		                	<td></td>
		                	<td><span class="pull-right"><b><?php echo __('Justified'). ': '?></b> <?php echo $justified; ?></span></td>
		          		</tr>
		          	</tfoot>
		        </table>
		    </div>
		<?php endif; ?>	
	</div>
	<div class="col-md-1"></div>
	<div class="col-md-12 container-buttons">
    	<?php echo button_to(__('Go back'), $link, array('class'=>'btn btn-default')) ?>
  	</div>
</div>
    