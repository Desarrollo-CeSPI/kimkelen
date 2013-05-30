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
  <div>
  <div class="logo"><?php echo image_tag("kimkelen_logo.png", array('absolute' => true)) ?></div>
  <div class="logo_fvet"><?php echo image_tag("fvet.jpeg", array('absolute' => true)) ?></div>
  <div class="school-name">Escuela de Educación Técnico Profesional </div>
  <div class="school-name">de Nivel Medio en Producción Agropecuaria y Agroalimentaria</div>
  <div class="school-name">Facultad de Ciencias Veterinarias de la UBA</div>
</div>

  <div class="header_row">
    <div class="title"><?php echo __('Student/a') ?>: </div>
    <div class="name"><?php echo $student ?></div>
    <div class="header_right">
      <div class="title"><?php echo __('Course') ?>: </div>
      <div class="course"><?php echo $division->getYear(); ?></div>
      <div class="title"><?php echo __('Division') ?>: </div>
      <div class="orientation"><?php echo $division->getDivisionTitle(); ?></div>
    </div>
    <div class="school-year"><?php echo __('ciclo lectivo') ?>: <?php echo $school_year; ?></div>
  </div>
</div>