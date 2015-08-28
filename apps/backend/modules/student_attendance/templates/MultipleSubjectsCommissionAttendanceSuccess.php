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
<h2><?php echo __('Course subjects of course %course%', array('%course%' => $course)) ?></h2>
<table style="width: 40%">
  <thead>
    <tr>
      <td><?php echo __('Course subject') ?></td>
      <td></td>
    <tr>
  </thead>
  <?php foreach ($course_subjects as $cs): ?>
    <tr>
      <td><?php echo $cs->getCareerSubjectSchoolYear() ?></td>
      <?php $career_school_year_id = $cs->getCareerSchoolYear()->getId(); ?>
      <?php $year = $cs->getYear(); ?>
      <?php $course_subject_id = $cs->getId(); ?>
      <td><?php echo link_to(__('Load Attendances'), "student_attendance/StudentAttendance?url=commission&year=$year&course_subject_id=$course_subject_id&career_school_year_id=$career_school_year_id&division_id="); ?></td>
    </tr>
  <?php endforeach ?>
</table>

<ul class="sf_admin_actions">
  <li class="sf_admin_action_list">
    <?php echo link_to(__("Back"), url_for("@commission")) ?>
  </li>
</ul>