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
  <div class="logo">
    <?php echo image_tag("logo-kimkelen1.png", array('absolute' => true)) ?>
    <?php echo image_tag("logo-kimkelen2.png", array('absolute' => true)) ?>    
  </div>  
  <div class="school-year"><?php echo __('ciclo lectivo') ?>: <?php echo $school_year; ?></div>
  <div class="header_row">
    <div class="title"><?php echo __('Student') ?>: </div>
    <div class="name"><?php echo $student." - ".$student->getPersonFullIdentification(); ?></div>
    <div class="header_right">
      <div class="title"><?php echo __('Course') ?>: </div>
      <!-- <div class="title">Año: </div> -->
      <div class="course"><?php echo $division->getYear(); ?></div>
      <div class="title"><?php echo __('Division') ?>: </div>
      <div class="division"><?php echo $division->getDivisionTitle(); ?></div>
      <?php if ($student_career->getOrientation()): ?>
        <div class="title"><?php echo __('Career orientation') ?>: </div>
        <div class="orientation"><?php echo (($student_career->getOrientation()) ? $student_career->getOrientation() : '-'); ?></div>
      <?php endif ?>
    </div>
  </div>
</div>