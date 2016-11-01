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
<?php include_partial('course_student_mark/pathway_info', array('student' => $course_subject_student->getStudent()))?>

<h5>Historial de notas:</h5>

<div class="mark-container-info">
  <?php foreach ($marks as $mark) :?>
    <div class="info-mark-container">
      <?php echo __('Mark %number%: %mark%', array('%number%' => $mark->getMarkNumber(), '%mark%' => $mark->getMarkByConfig()))?>
    </div>
  <?php endforeach?>

  <div class="info-mark-container">
    <?php echo __('Average: %average%', array('%average%' => $course_subject_student->getCourseResult()->getResultStr())) ?>
  </div>

  <?php foreach ($course_subject_student_examinations as $course_subject_student_examination):?>
    <div class="info-mark-container">
      <?php echo __('Examination %examination%: %mark%', array('%examination%' => $course_subject_student_examination->getExaminationSubject()->getExamination(),
          '%mark%'=> $course_subject_student_examination->getIsAbsent()? __('Absent') : $course_subject_student_examination->getMarkStrByConfig())); ?>
    </div>
  <?php endforeach ?>

  <?php foreach ($student_examination_repproved_subjects as $student_examination_repproved_subject):?>
    <div class="info-mark-container">
      <?php echo __('Repproved examination %repproved_examination%: %mark%', array('%repproved_examination%' => $student_examination_repproved_subject->getExaminationRepprovedSubject()->getExaminationRepproved(),
          '%mark%'=> $student_examination_repproved_subject->getIsAbsent()? __('Absent'): $student_examination_repproved_subject->getMarkStrByConfig())); ?>
    </div>
  <?php endforeach ?>
</div>
