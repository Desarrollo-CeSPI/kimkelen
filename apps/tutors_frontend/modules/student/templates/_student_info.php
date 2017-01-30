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

	<div class="col-md-7 container-sombra-exterior box-student">
		<div class="student-name">
			<b><?php echo $student->getPerson()->getFullName(); ?> </b>
		</div>
		<div class="container-sombra-exterior student-info">
			<b><?php echo $student->getPerson()->getFullIdentification(); ?></b>
		</div>
		<div class="container-sombra-exterior student-info">	
			<span class="glyphicon glyphicon glyphicon-phone" aria-hidden="true"></span>
			<span> <b> <?php echo __("Phone") .': '?> </b><?php echo $student->getPerson()->getPhone();?></span>
		</div>
		<div class="container-sombra-exterior student-info">
			<span class="glyphicon glyphicon glyphicon-envelope" aria-hidden="true"></span>
			<span> <b> <?php echo __("Email") .': ' ?> </b>  <?php echo ($student->getPerson()->getEmail())? $student->getPerson()->getEmail():' - '; ?> </span>
		</div>
		<div class="container-sombra-exterior student-info">
			<span class="glyphicon glyphicon glyphicon-map-marker" aria-hidden="true"></span> 
			<span> <b> <?php echo __("Address") .': ' ?> </b> <?php echo ($student->getPerson()->getAddress())? $student->getPerson()->getAddress()->getFullAddress(): ' - '; ?> </span>
		</div>	
	</div>