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
	<div class="col-md-12">
		<div class="col-md-1"></div>
		<div class="col-md-10 container-sombra-exterior container-sanctions">
			<div class="student-info title-box sanction">	
				<span class="glyphicon glyphicon-exclamation-sign icon-title large red" aria-hidden="true"></span>
				<span class="title-sanctions"><?php echo __('Disciplinary sanctions'); ?> |</span>
				<span class=""><?php echo $student . ' - ' . __('school year') . ' '. $school_year->getYear()?></span>
			</div>
			<?php $total= 0 ;?>
			<?php foreach ($sanctions_type as $st): ?>
				<div class="container-sombra-exterior student-info">
					<span> <b> <?php echo $st->getName() .': '?> </b></span> <span class="pull-right"><?php echo $info[$st->getName()] ;?></span>
				</div>
				<?php $total+= $info[$st->getName()] ; ?>
			<?php endforeach ?>
			<div class="container-sombra-exterior student-info">
				<span> <b> Total: </b></span> <span class="pull-right"><?php echo $total ;?></span>
			</div>

		</div>
		<div class="col-md-1"></div>
	</div>

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
					<?php echo link_to(__('Disciplinary sanctions report'), 'student_disciplinary_sanction/showReport?student_id=' . $student->getId());?>
				</div>
			</div>
		</div>
		<div class="col-md-1"></div>	
	</div>

</div>
