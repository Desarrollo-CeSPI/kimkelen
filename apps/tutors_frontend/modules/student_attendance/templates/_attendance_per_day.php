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
	<?php if(is_null($division)):?>
		<div class="info-attendance"><?php echo __('No se registraron inasistencias para este alumno.'); ?></div>
	<?php else: ?>
	<?php $periods = CareerSchoolYearPeriodPeer::getTrimesterPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>
 			<div class="col-md-1"></div>
 			<div class="col-md-10">
 				<div class="table-responsive"> 
					 <table class="table">
						 <thead>
						  <tr class="success">
							 <th><?php echo __('Periodo') ?></th>
							 <th><?php echo __('Inasistencias') ?></th>
						  </tr>
						 </thead>
						 <tbody>
							<?php foreach ($periods as $period): ?>
							<tr>
							<?php $absences = count(StudentAttendancePeer::retrieveByStudentAndPeriod($student,$period)); ?>
							  <td><?php echo $period->getName();?></td>
					          <td><?php echo round($absences, 2) ?></td>
					          <?php $total += $absences ?>
							</tr>
						  <?php endforeach; ?>
						 </tbody>
						 <tfoot>
						  <tr>
							 <td><b>Total</b></td>
							 <td><b><?php echo $total ?></b></td>
						  </tr>
						 </tfoot>
					</table> 
				</div>
			</div>
			<div class="col-md-1"></div>
			<?php endif ?>
