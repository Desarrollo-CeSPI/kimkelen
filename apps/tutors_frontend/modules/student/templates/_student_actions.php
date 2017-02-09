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

	<div class="col-md-5">
		<div class="col-md-12 box-action box-califications">
			<div class="icon-action lightblue">
				<span class="glyphicon glyphicon-star large" aria-hidden="true"></span>
			</div>
			<div class="title">
				<div class="title-action">
					<b><?php echo link_to(__('Califications'), 'califications/showHistory?student_id=' . $student->getId());?> </b>
				</div>
				<div class="info-action">
					<i><?php echo "Consulte el historial académico de calificaciones"; ?> </i>
				</div>
			</div>			
		</div>
		<div class="col-md-12 box-action box-attendance">
			<div class="icon-action violet">
				<span class="glyphicon glyphicon-file large" aria-hidden="true"></span>
			</div>
			<div class="title">
				<div class="title-action">
					<b><?php echo link_to(__('Attendance'), 'student_attendance/index?student_id=' . $student->getId());?></b>
				</div>
				<div class="info-action">
					<i><?php echo "Verfique el registro de inasistencias a clases"; ?></i>
				</div>
			</div>
		</div>
		<div class="col-md-12 box-action sanction">
			<div class="icon-action red">
				<span class="glyphicon glyphicon-exclamation-sign large" aria-hidden="true"></span> 
			</div>
			<div class="title">
				<div class="title-action">
					<b><?php echo link_to(__('Disciplinary sanctions'), 'student_disciplinary_sanction/showHistory?student_id=' . $student->getId()); ?></b>
				</div>
				<div class="info-action">
					<i> <?php echo "Chequee las sanciones disciplinarias imputadas"; ?> </i>
				</div>
			</div>
		</div>
	</div>