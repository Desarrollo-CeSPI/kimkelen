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
<?php foreach ($course_subject_students as $course_subject_student): ?>
  <div class="sf_admin_form_row">
    <?php include_partial('course_student_mark/free_info', array('course_subject_student' => $course_subject_student))?>
    <label for="course_student_mark[<?php echo $course_subject_student->getId() ?>]" class="required">
      <?php echo strval($course_subject_student->getStudent()) ?>
    </label>
    <div>

      <?php include_component('course_student_mark', 'mark', array('form' => $form, 'course_subject_student' => $course_subject_student, 'course_subject' => $course_subject, 'configuration' => $configuration)) ?>


      <?php include_component('course_student_mark', 'component_close', array('form' => $form, 'course_subject_student' => $course_subject_student, 'course_subject' => $course_subject, 'configuration' => $configuration)) ?>
      <div style="clear: both; font-size: 1px; height: 1px;">
      </div>
    </div>
  </div>
<?php endforeach; ?>