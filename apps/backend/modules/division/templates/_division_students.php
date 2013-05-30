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
<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_course_subject_student_list">
  <div>
    <label><?php echo __("Alumnos inscriptos") ?></label>
    <?php if (count($students = $division->getStudents())): ?>
      <ul style="margin-left: 9em;">
        <?php foreach ($students as $s): ?>
          <li><?php echo $s ?></li>
        <?php endforeach ?>
      </ul>
    <?php else: ?>
      <?php echo __('El curso no tiene alumnos inscriptos.') ?>
    <?php endif ?>
  </div>
</div>