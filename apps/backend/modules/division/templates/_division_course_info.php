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
<div class="sf_admin_form_row sf_admin_List sf_admin_form_field_courses">
  <div>
    <label for="course"> <?php echo __('Courses');?> </label>
    <ul>
      <?php foreach ($division->getCourses() as $course):?>
        <li><?php echo link_to(__('Go to %course%', array('%course%' => $course)),'@shared_course_show?id=' . $course->getId())?></li>
      <?php endforeach ?>
    </ul>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>