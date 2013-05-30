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
<?php include_partial('student/assets') ?>
<?php use_stylesheet('history.css', 'first') ?>

<div id="sf_admin_container">
  <h1><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject() ?></h1>

  <div id="sf_admin_content">
    <ul class="sf_admin_actions">
      <li class="sf_admin_action_list">
        <?php echo link_to(__("Back"), $back_url) ?>
      </li>
    </ul>
    
    <?php include_partial("student/history_details_course", array("course_subject_student" => $course_subject_student)) ?>
    
    <?php foreach ($course_subject_student->getCourseSubjectStudentExaminations() as $course_subject_student_examination): ?>
      
      <?php include_partial("student/history_details_course_subject_student_examination", array("course_subject_student_examination" => $course_subject_student_examination)) ?>
      
    <?php endforeach ?>
    
    <?php foreach ($course_subject_student->getStudentRepprovedCourseSubjects() as $repproved): ?>
      <?php foreach ($repproved->getStudentExaminationRepprovedSubjects() as $rep): ?>
        <?php include_partial("student/history_details_student_examination_repproved_subject", array("student_examination_repproved_subject" => $rep)) ?>
      <?php endforeach ?>
    <?php endforeach ?>
    
    <?php if ($course_subject_student->getCourseSubject()->getCourse()->getIsClosed()): ?>
      <div class="history_details">
        <h2><?php echo __("Final mark: %%final_mark%%", array("%%final_mark%%" => $course_subject_student->getFinalMark())) ?></h2>
      </div>
    <?php endif ?>
    
    <ul class="sf_admin_actions">
      <li class="sf_admin_action_list">
        <?php echo link_to(__("Back"), $back_url) ?>
      </li>
    </ul>
  </div>
</div>