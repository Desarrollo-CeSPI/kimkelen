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
<?php if ($course_subject_student_examination->getExaminationSubject()): ?>
      
  <div class="history_details">
    <h2><?php echo $course_subject_student_examination->getExaminationSubject()->getExamination()->getName() ?></h2>
  
    <div class="info_div">
      <strong><?php echo __("Instance") ?></strong> <em><?php echo $course_subject_student_examination->getExaminationNumber() ?></em>
    </div>
    <?php if (!$course_subject_student_examination->getIsAbsent()): ?>
      <div class="info_div">
        <strong><?php echo __("Mark") ?></strong> <em><?php echo ($mark = $course_subject_student_examination->getMark()) ? $mark : "-" ?></em>
      </div>
    <?php else: ?>
      <div class="info_div">
        <strong><?php echo __("Is absent") ?></strong>
      </div>
    <?php endif ?>
    <div class="info_div">
      <strong><?php echo __("Fecha") ?></strong> <em><?php echo $course_subject_student_examination->getDate('d/m/y')?></em>
    </div>
    <div class="info_div">
      <strong><?php echo __("Status") ?></strong> <em><?php echo __($course_subject_student_examination->getExaminationSubject()->getIsClosed() ? $course_subject_student_examination->getResultString() : "Examination subject is not closed yet.") ?></em>
    </div>
  </div>
  
<?php else: ?>
  
  <div class="info_div">
    <strong><?php echo __("The subject is not approved yet. The student is not inscripted to any examination.") ?></strong>
  </div>
  
<?php endif ?>