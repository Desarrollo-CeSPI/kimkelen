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

	<div class="col-md-12 container-students">
		<div class="student-box-info">
			<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
			<span class="text-student"> <?php echo __("Students in charge");?> </span>
		</div>

		<?php foreach ($students as $s): ?>
			
			<div class="button-student">
				<?php echo link_to(__($s->getPerson()->getFullName()),'student/index?student_id=' . $s->getId()) ?>
			</div>
		<?php endforeach;?>

	</div>


