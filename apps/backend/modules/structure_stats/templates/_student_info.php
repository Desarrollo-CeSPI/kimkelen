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
<?php $person = $student->getPerson() ?>
<div class="student_head">
  <div class="person_name"><strong><?php echo $student ?></strong></div>
</div>
<div class="student_info">
  <div class="student_personal_info">
    <div class="info_div"><strong><?php echo $student->getPersonFullIdentification() ?></strong></div>
    <div class="info_div"><strong><?php $person->getEmail() != '' and print __('Email %email%', array('%email%' => $person->getEmail())) ?></strong></div>
    <div class="info_div"><strong><?php $person->getPhone() != '' and print __('Phone %phone_number%', array('%phone_number%' => $person->getPhone())); ?></strong></div>
  </div>
  <div class="student_current_info">
    <div class="info_div">
      <div>
        <div class="info_div_label"><?php echo $student->getCareerFromStudentStatsFilters() ?></div>
      </div>
    </div>
    <div class="info_div">
      <span class="info_div_label"><?php echo __('Division/s') ?></span>
      <span><?php echo $student->getDivisionFromStudentStatsFilters() ?></span>
    </div>
    <div class="info_div">
      <span class="info_div_label"><?php echo __('Shift') ?></span>
      <span><?php echo $student->getShiftFromStudentStatsFilters() ?></span>
    </div>