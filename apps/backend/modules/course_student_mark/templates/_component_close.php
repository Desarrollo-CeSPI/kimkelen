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
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<?php if (($course_subject_student->canClose() || $course_subject_student->isClosed()) && ($sf_user->hasCredential('close_student_mark')) && (isset($form[$course_subject_student->getId() . '_close']))) :?>
<div class="close_student" id="close_student_<?php echo $course_subject_student->getId()?>">
    <?php if ($course_subject_student->isClosed()): ?>
      <?php echo __("Student closed.")?>
    <?php else: ?>
      <?php echo $form[$course_subject_student->getId() . '_close']->render()?>
      <?php echo __("Close course student ?")?>
    <?php endif ?>
    
    <span  class="close_student_result" id="<?php echo $course_subject_student->getId() . '_close_div'?>" style="display: <?php echo $course_subject_student->isClosed() ? 'display':'none'?>">
      <?php echo $form[$course_subject_student->getId().'_result']->render() ?>
    </span>   
  </div>
<?php endif ?>