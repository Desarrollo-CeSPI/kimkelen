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
<div class="report-header">
  <div class="header_row">
      <div class="logo"><?php echo image_tag("logo-kimkelen-negro.png", array('width' => 240, 'height' => 70)) ?></div>
	  <div class="pair">
	    <div class="title"><?php echo __('Student') ?>: </div>
      <div class="name"><?php echo $student ?></div>
	    <div class="header_right">

	      <div class="title"><?php echo __('Grado') ?>: </div>
	      <div class="course"><?php echo $division->getYear(); ?>° <?php echo $division->getDivisionTitle(); ?></div>

	       <div class="title"><?php echo __('School year') ?>: </div>
	       <div class="school_year"><?php echo $school_year; ?></div>
	    </div>
    </div>
  </div>
</div>