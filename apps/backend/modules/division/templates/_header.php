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
    <div class="logo"><?php echo image_tag("kimkelen_logo.png", array('absolute' => true)) ?></div>
    <div class="header_row">
      <h2><?php echo __('Print averages'); ?></h2>
      <div class="title2"><?php echo __('School year') ?>: </div>
      <div class="row-content"><?php echo $division->getSchoolYear() ?></div>
      <div class="title2"><?php echo __('Año/Nivel') ?>: </div>
      <div class="row-content"><?php echo $division->getYear() ?></div> 
      <div class="title2"><?php echo __('Division') ?>: </div>
      <div class="row-content"><?php echo $division->getDivisionTitle(); ?></div>
      <div class="row-content"></div>
    </div>
</div>