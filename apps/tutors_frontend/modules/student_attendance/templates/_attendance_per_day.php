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
 */
?>

<?php $periods = CareerSchoolYearPeriodPeer::getTrimesterPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>
  <div class="table-responsive">
	<table class="table table-striped table-bordered">
	  <thead>
		<tr>
		  <th><?php echo __('Periodo') ?></th>
		  <th><?php echo __('Total') ?></th>
		</tr>
	  </thead>
	  <tbody>
	  <?php foreach ($periods as $period): ?>
		<?php $absences= $student->getTotalAbsences($student_career_school_year->getCareerSchoolYear()->getId(), $period, null, false) ; ?>
		<tr>
		  <td><?php echo $period->getName();?></td>
		  <td><?php echo round($absences, 2) ?></td>
		</tr>
	  <?php endforeach; ?>
	  </tbody>
	  <tfoot>
		<tr>
		  <td> <strong>Total</strong> </td>
		  <td> <strong><?php echo $student->getTotalAbsences($student_career_school_year->getCareerSchoolYear()->getId(), null, null, false)  ?></strong> </td>
		</tr>
	  </tfoot>
	</table>
  </div>
