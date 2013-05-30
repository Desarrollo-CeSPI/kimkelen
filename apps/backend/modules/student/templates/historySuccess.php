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
  <h1><?php echo __("%%student%%'s history in %%career_student%%", array("%%student%%" => $career_student->getStudent(), "%%career_student%%" => $career_student->getCareer()))?></h1>

  <div id="sf_admin_content">
    <ul class="sf_admin_actions">
      <li class="sf_admin_action_list">
        <?php echo link_to(__("Back"), "student/registerForCareer?id=".$career_student->getStudentId()) ?>
      </li>
    </ul>

    <?php if (count($career_student->getStudent()->getStudentCareerSchoolYears())): ?>

      <?php foreach ($career_student->getStudent()->getStudentCareerSchoolYears() as $student_career_school_year): ?>
        <?php include_partial("student/history_school_year", array("career_student" => $career_student, "student_career_school_year" => $student_career_school_year)) ?>
      <?php endforeach ?>

    <?php else: ?>

        <strong><?php echo __("The student were not registered to any school year.") ?></strong>

    <?php endif ?>

    <ul class="sf_admin_actions">
      <li class="sf_admin_action_list">
        <?php echo link_to(__("Back"), "student/registerForCareer?id=".$career_student->getStudentId()) ?>
      </li>
    </ul>
  </div>
</div>