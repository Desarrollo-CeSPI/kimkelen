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
<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_course_subject_list">
  <div>
    <label><?php echo __("Materias") ?></label>
    <?php if (count($courses = $division->getCourses())): ?>
      <ul style="margin-left: 9em;">
        <?php foreach ($courses as $course): ?>
          <?php foreach ($course->getCourseSubjects() as $course_subject): ?>
            <li><?php echo $course_subject->getCareerSubject()->getSubjectName() ?></li>
          <?php endforeach ?>
        <?php endforeach ?>
      </ul>
    <?php else: ?>
      <?php echo __('La división no tiene materias.') ?>
    <?php endif ?>
    <div class="help">
      <?php echo __("Éste listado incluye aquellas materias que son opción de otra.") ?>
    </div>
  </div>
</div>