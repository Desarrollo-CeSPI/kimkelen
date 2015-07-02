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
<div class="mark-container-info">
  <?php foreach ($marks as $mark) :?>
    <div class="mark-container">
      <?php $subject_configuration = $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getConfiguration(); ?>
      <?php if(($subject_configuration->getEvaluationMethod() == EvaluationMethod::FINAL_PROM) && ($mark->getMarkNumber() == $subject_configuration->getCourseMarks())): ?>
        <?php echo __('Final mark: %final_mark%', array('%final_mark%' => $mark->getStringMark())); ?>&nbsp;&nbsp;
      <?php else: ?>
        <?php echo __('Mark %number%: %mark%', array('%number%' => $mark->getMarkNumber(), '%mark%' => $mark->getStringMark())); ?>&nbsp;&nbsp;
      <?php endif; ?>
      <?php echo $mark->renderChangelog(); ?>
    </div>
  <?php endforeach?>

  <div class="mark-container">
    <?php echo __('Average: %average%', array('%average%' => $course_subject_student->getMarksAverage())); ?>
  </div>

  <?php foreach ($course_subject_student_examinations as $course_subject_student_examination):?>
    <div class="mark-container">
      <?php echo __('Examination %examination%: %mark%', array('%examination%' => $course_subject_student_examination->getExaminationSubject()->getExamination(),
          '%mark%'=> $course_subject_student_examination->getIsAbsent()? __('Absent') : $course_subject_student_examination->getMark())); ?>
      <?php echo ncChangelogRenderer::render($course_subject_student_examination, 'tooltip', array('credentials' => 'view_changelog')); ?>
    </div>
  <?php endforeach ?>

  <?php foreach ($student_examination_repproved_subjects as $student_examination_repproved_subject):?>
    <div class="mark-container">
      <?php echo __('Repproved examination %repproved_examination%: %mark%', array('%repproved_examination%' => $student_examination_repproved_subject->getExaminationRepprovedSubject()->getExaminationRepproved(),
          '%mark%'=> $student_examination_repproved_subject->getIsAbsent()? __('Absent'): $student_examination_repproved_subject->getMark())); ?>

      <?php echo ncChangelogRenderer::render($student_examination_repproved_subject, 'tooltip', array('credentials' => 'view_changelog')); ?>
    </div>
  <?php endforeach ?>
</div>