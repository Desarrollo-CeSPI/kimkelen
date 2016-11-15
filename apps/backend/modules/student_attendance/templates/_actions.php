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
<ul class="sf_admin_td_actions">
  <?php if ($sf_user->hasCredential('set_free')):?>
    <li>
      <?php echo link_to("Dejar libre", "student_attendance/free?student_id=" . $student->getId());?>
    </li>
    <?php if ($student->isFree(null, $course_subject, $career_school_year)):?>
      <li>
        <?php $course_subject_id = is_null($course_subject) ? '' : $course_subject->getId() ?>
      
        <?php echo link_to(__("Reincorporación"), 
          "student_attendance/reincorporate?student_id=" . $student->getId() . 
          '&course_subject_id=' . $course_subject_id);?>
      </li>
    <?php endif?>
  <?php endif?>
  <li>
	  <?php echo link_to("Ver planilla de inasistencias", 'student/showAssistanceSheet?id='. $student->getId());?>
  </li>
</ul>
