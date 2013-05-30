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
<?php $subject_configuration = $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear()->getConfiguration(); ?>
<?php foreach($marks as $mark): ?>
  <?php $field = $form[$course_subject_student->getId().'_'.$mark->getMarkNumber()]; ?>
  <?php $request_value = $sf_request->getParameter($form->getName().'['.$course_subject_student->getId().'_'.$mark->getMarkNumber().']'); ?>
  <div class='mark-container'>
    <?php if(($subject_configuration->getEvaluationMethod() == EvaluationMethod::FINAL_PROM) && ($mark->getMarkNumber() == $subject_configuration->getCourseMarks())): ?>
      <?php echo __('Final mark', array('%d' => $mark->getMarkNumber()));?>:&nbsp;&nbsp;
    <?php else: ?>
      <?php echo __('Mark %number%', array('%number%' => $mark->getMarkNumber())); ?>:&nbsp;&nbsp;
    <?php endif; ?>
    <?php echo $field->render(array('class' => 'mark'.($field->hasError() ? ' with-error' : ''), 'value' => ((isset($request_value) && $request_value)?$request_value:$field->getValue()))); ?>

    <?php if ($field->hasError()): ?>
      <?php echo $field->renderError(); ?>
    <?php endif; ?>

     <?php if(!$mark->getIsClosed()):?>
      <?php echo __($mark->getFreeLabel())?>
      <?php echo $form[$course_subject_student->getId().'_free_'.$mark->getMarkNumber()]->render()?>

    <?php endif?>

    <?php echo $mark->renderChangelog(); ?>
  </div>
<?php endforeach; ?>