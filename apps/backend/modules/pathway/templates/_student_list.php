<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2015 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
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
<div class="sf_admin_form_row">
  <div>
    <label for="student_list"><?php echo __('Alumnos marcados para esta trayectoria'); ?></label>
  </div>
  <?php if(!$pathway->getPathwayStudents()):?>
    <?php echo __('No se marcó ningún alumno para esta trayectoria'); ?>
  <?php else:?>
    <table>
	    <th></th>
	    <th><?php echo __('Año para el cual está inscripto') ?></th>
      <?php foreach ($pathway->getPathwayStudents() as $pathway_student): ?>
        <tr>
	        <td><?php echo $pathway_student->getStudent(); ?></td>
	        <td><?php echo $pathway_student->getYear(); ?>°</td>
        </tr>
      <?php endforeach  ?>
    </table>
  <?php endif?>
  <div style="margin-top: 1px; clear: both"></div>
</div>