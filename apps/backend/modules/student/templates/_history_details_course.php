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
<div class="history_details">
  <h2><?php echo __("Course: %%course%%", array("%%course%%" => $course_subject_student->getCourseSubject()->getCourse())) ?></h2>
      
  <?php foreach ($course_subject_student->getCourseSubjectStudentMarks() as $course_subject_student_mark): ?>
    <div class="info_div">
      <strong><?php echo __("Mark %%mark_number%%", array("%%mark_number%%" => $course_subject_student_mark->getMarkNumber())) ?></strong> <em><?php echo ($mark = $course_subject_student_mark->getMark()) ? $mark : "-" ?></em>
    </div>
  <?php endforeach ?>

  <div class="info_div">
    <strong><?php echo __("Average") ?></strong> <em><?php echo ($avg = $course_subject_student->getMarksAverage()) ? $avg : "-" ?></em>
  </div>

  <div class="info_div">
    <strong><?php echo __("Final status") ?></strong> <em><?php echo __($course_subject_student->getStatus()) ?></em>
  </div>
</div>