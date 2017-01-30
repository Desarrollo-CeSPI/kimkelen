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
	<?php if(is_null($division)):?>
		<div class="info-attendance">El alumno no registra inasistencias en el año lectivo vigente</div>
	<?php else: ?>
	<?php $periods = CareerSchoolYearPeriodPeer::getTrimesterPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>
 			<div class="col-md-1"></div>
 			<div class="col-md-10">
 				<div class="table-responsive"> 
 					<table class="table table-condensed">
				    	<tr class="success">
				    	  <th><?php echo __('Primer Trimestre') ?></th>
					      <th><?php echo __('Segundo Trimestre') ?></th>
					      <th><?php echo __('Tercer Trimestre') ?></th>
					      <th>Total</th>
					    </tr>
					    <tr>
					      <?php $total = 0; ?>
					      <?php foreach ($periods as $period): ?>
					        <?php $absences = count(StudentAttendancePeer::retrieveByStudentAndPeriod($student,$period)); ?>
					          <td><?php echo round($absences, 2) ?></td>
					          <?php $total += $absences ?>
					      <?php endforeach; ?>
					      <td><?php echo round($total, 2) ?></td>
					    </tr>
					 </table>
				</div>
			</div>
			<div class="col-md-1"></div>
			<?php endif ?>
