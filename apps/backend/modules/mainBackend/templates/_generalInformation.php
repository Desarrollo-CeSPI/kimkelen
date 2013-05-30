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
<div class="box general-information">
  <h2><?php echo __('Información general') ?></h2>
  
  <div class="content">
    <ul class="items-list">
      <li class="item">
        <strong><?php echo __('Año lectivo vigente') ?>:</strong>
        <?php if (is_null($current_school_year)): ?>
          <?php echo __('No hay un año lectivo vigente') ?>
        <?php else: ?>
          <?php echo strval($current_school_year) ?>
        <?php endif ?>
      </li>

      <li class="item">
        <strong><?php echo __('Cantidad de alumnos matriculados en el año lectivo vigente:')?></strong>
        <?php echo $amount_sy_students?>
      </li>

      <li class="item">
        <strong><?php echo __('Cantidad de alumnos en el sistema:')?></strong>
        <?php echo $amount_students?>
      </li>

      <li class="item">
        <strong><?php echo __('Cantidad de docentes en el sistema:')?></strong>
        <?php echo $amount_teachers?>
      </li>

    </ul>
  </div>
</div>