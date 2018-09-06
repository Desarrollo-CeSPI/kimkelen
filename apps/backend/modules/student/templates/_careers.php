<?php /*
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
<?php use_helper('I18N') ?>

<?php if ($cs = $student->getLastCareerStudent()): ?>
  <div class="student_careers">
    <strong><?php echo __('Career') ?></strong>
    <div style="margin-left:5px"><?php echo $cs->getCareer() ?></div>
    <div  style="margin-left:5px" class="student_career_orientation"><?php $cs->getOrientation() != '' and print __('Orientation: %orientation%', array('%orientation%' => $cs->getOrientation())) ?></div>
    <div style="margin-left:10px" class="student_career_orientation"><?php $cs->getSubOrientation() != '' and print __('Sub orientation: %sub_orientation%', array('%sub_orientation%' => $cs->getSubOrientation())) ?></div>
    <?php if ($cs->isRegular() && ($cs->getCurrentStudentCareerSchoolYear())): ?>
      <div class="student_year"><?php echo __('Year: %year%', array('%year%' => $cs->getCurrentStudentCareerSchoolYear()->getYear())) ?></div>
      <div class="student_career_repproved"><?php $student->isRepproved() and print __('Repproved') ?></div>
      <div class="student_career_withdraw"><?php $student->getCurrentOrLastStudentCareerSchoolYear()->IsWithdraw() and print __('Withdraw') ?></div>
      <div class="student_career_withdraw"><?php $student->getCurrentOrLastStudentCareerSchoolYear()->IsWithdrawWithReserve() and print __('Withdraw with reserve') ?></div>
      <div class="student_career_free"><?php $student->getCurrentOrLastStudentCareerSchoolYear()->IsFree() and print __('Free') ?></div>
      <?php if($student->getJudicialRestriction()):?>
      <div class="student_judicial_restriction"> <?php echo __('Judicial restriction')?></div>
      <?php endif ;?>
    <?php elseif ($cs->isGraduate()): ?>
      <div class="student_career_graduate"><strong><?php echo __('Graduate') ?></div>
    <?php endif ?>
  </div>
<?php endif ?>
