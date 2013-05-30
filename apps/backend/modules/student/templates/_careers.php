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
<?php use_helper('I18N')?>

<?php echo __('Career')?>
<div class="student_careers">
  <?php $cs = $student->getLastCareerStudent(); ?>
    <div><?php echo $cs->getCareer() ?></div>
    <div class="student_career_orientation"><?php $cs->getOrientation() != '' and print __('Orientation: %orientation%', array('%orientation%' => $cs->getOrientation())) ?></div>
    <div class="student_career_orientation"><?php $cs->getSubOrientation() != '' and print __('Sub orientation: %sub_orientation%', array('%sub_orientation%' => $cs->getSubOrientation())) ?></div>
    <?php if ($cs->isRegular() && ($cs->getCurrentStudentCareerSchoolYear())): ?>
      <div class="student_year"><?php echo __('Year: %year%', array('%year%' => $cs->getCurrentStudentCareerSchoolYear()->getYear()))?></div>
      <div class="student_career_repproved"><?php $cs->getCurrentStudentCareerSchoolYear()->getIsRepproved() and print __('Repproved') ?></div>
    <?php elseif ($cs->isGraduate()): ?>
      <div class="student_career_graduate"><strong><?php echo __('Graduate')?></div>
    <?php endif ?>

</div>