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
<div id="sf_admin_container">
  <h1><?php echo __('Student school years %student%', array('%student%' => $student)) ?></h1>
  <table style='width: 100%'>
    <thead>
      <tr>
        <th><?php echo __('School year') ?></th>
        <th><?php echo __('Career') ?></th>
        <th><?php echo __('Year') ?></th>
        <th><?php echo __('Status') ?></th>
        <th></th>
        <th></th>
      <tr>
    </thead>
    <?php foreach ($student_career_school_years as $student_career_school_year): ?>
      <tr>
        <td><?php echo $student_career_school_year->getCareerSchoolYear()->getSchoolYear() ?></td>
        <td><?php echo $student_career_school_year->getCareerSchoolYear()->getCareer() ?></td>
        <td><?php echo $student_career_school_year->getYear() ?></td>
        <td><?php echo __($student_career_school_year->getStatusString()) ?></td>
        <?php if (count($student->getCurrentDivisions($student_career_school_year->getCareerSchoolYear()->getId())) != 0) : ?>
          <td><?php echo link_to(__('Imprimir periodos cerrados'), 'report_card/printStudentObservationsCard?student_career_school_year_id=' . $student_career_school_year->getId()) ?></td>
          <td><?php echo link_to(__('Imprimir todos los periodos'), 'report_card/printStudentObservationsAllPeriod?student_career_school_year_id=' . $student_career_school_year->getId()) ?></td>
        <?php endif; ?>
      </tr>
    <?php endforeach ?>
  </table>
  <ul class="sf_admin_actions">
    <li class="sf_admin_action_list">
      <?php echo link_to(__("Back"), url_for("@student")) ?>
    </li>
  </ul>
</div>
