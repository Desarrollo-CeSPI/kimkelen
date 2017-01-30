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
<div class="col-md-12 container-sombra">
	<div class="col-md-5">
		<div class="col-md-2"></div>
		<div class="col-md-10">
			<div class="tutor-identification">
				<?php echo $tutor->getPerson()->getFullName(); ?>
			</div>
			<div class="tutor-documentation">
				<?php echo $tutor->getPerson()->getFullIdentification(); ?>
			</div>
		</div>
		
	</div>
	<div class="col-md-7">
		<div class="tutor-box">
			<div class="tutor-box-info">
				<span class="glyphicon glyphicon glyphicon-phone lightgreen" aria-hidden="true"></span>
				<span class="text"> <b> <?php echo __("Phone") .': ' ?> </b>  <?php echo $tutor->getPerson()->getPhone(); ?> </span>
			</div>
			<div class="tutor-box-info">
				<span class="glyphicon glyphicon glyphicon-envelope lightgreen" aria-hidden="true"></span>
				<span class="text"> <b> <?php echo __("Email") .': ' ?> </b>  <?php echo $tutor->getPerson()->getEmail(); ?> </span>
			</div>
			<div class="tutor-box-info">
				<span class="glyphicon glyphicon glyphicon-map-marker lightgreen" aria-hidden="true"></span> 
				<span class="text"> <b> <?php echo __("Address") .': ' ?> </b>  <?php echo $tutor->getPerson()->getAddress()->getFullAddress(); ?> </span>
			</div>		
		</div>
	</div>	
</div>



		