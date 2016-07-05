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
<div class="student_tutor">
  <h2><?php echo __("Tutors") ?></h2>
  <?php if($student->getStudentTutors()): ?>
    <?php foreach($student->getStudentTutors() as $st): ?>
      <?php if($st->getTutor()->getIsAlive()): ?>
        <div class="tutor">
          <div class="tutor_name"><strong><?php echo $st->getTutor(); $st->getTutor()->getTutorType() != '' and print ' (' . __('%tutor_type%', array('%tutor_type%' => $st->getTutor()->getTutorType())) . ')'; ?></strong></div>
          <?php $person = $st->getTutor()->getPerson();?>
          <ul>
            <li><div class="tutor_occupation"><?php $st->getTutor()->getOccupation() != '' and print __('Occupation %occupation%', array('%occupation%' => $st->getTutor()->getOccupation())) ?></div></li>
            <li><div class="tutor_email"><?php $person->getEmail() != '' and print __('Email %email%', array('%email%' => $person->getEmail())) ?></div></li>
            <li><div class="tutor_phone"><?php $person->getPhone() != '' and print __('Phone %phone_number%', array('%phone_number%' => $person->getPhone())); ?></div></li>
          </ul>
        </div>
      <?php endif ?>
    <?php endforeach ?>
  <?php else: ?>
    <div class="tutor_empty"><strong><?php print __('this student has no tutors') ?></strong></div>
  <?php endif ?>
  <div class="emergency_information"><?php $student->getEmergencyInformation() != '' and print __('Emergency information %extra_information%', array('%extra_information%' => $student->getEmergencyInformation())); ?></div>
</div>