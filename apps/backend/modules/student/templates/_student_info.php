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
<?php $person = $student->getPerson()?>

<div class="student_head">
  <div class="person_name"><strong><?php echo link_to($student, 'student/show?id=' . $student->getId())?></strong></div>

	<div class="active"><strong><?php echo __("Is Active?") ?></strong> 
		<em><?php include_partial("student/is_active", array("student" => $student)) ?></em>	
		
		<?php if ($student->getHealthInfoString() == HealthInfoStatus::HEALTH_INFO_NO_COMMITED): ?>
		<div class="health"><strong> <?php echo __("Health card not received") ;?></strong></div>
		<?php elseif($student->getHealthInfoString() == HealthInfoStatus::HEALTH_INFO_NO_SUITABLE):?>
		<div class="health"><strong> <?php echo __("No suitable"); ?></strong></div>
			<?php elseif($student->getHealthInfoString() == HealthInfoStatus::HEALTH_INFO_NO_SUITABLE_ACCIDENT):?>
			<div class="health"><strong> <?php echo $student->getHealthInfoString(); ?></strong></div>
		<?php endif; ?>	
	</div>
	
	<?php if ($student->getBelongsToPathway()): ?>
	<div class="pathway"><strong><?php echo __("Pathway") ?></strong></div>
		<?php endif; ?>
	
	
</div>

<?php if (!($sf_user->isTeacher())): ?>
<div class="student_info">
  <div class="student_personal_info">
    <div class="info_div"><strong><?php echo $student->getPersonFullIdentification() ?></strong></div>
    <div class="info_div"><strong><?php $person->getEmail() != '' and print __('Email %email%', array('%email%' => $person->getEmail())) ?></strong></div>
    <div class="info_div"><strong><?php $person->getPhone() != '' and print __('Phone %phone_number%', array('%phone_number%' => $person->getPhone())); ?></strong></div>
    <div class="info_div"><?php  include_partial("student/tutors", array("student" => $student)) ?></div>
  </div>
  <?php endif; ?>

  <div class="student_current_info">
    <div class="info_div"><strong><?php echo __("Is registered?") ?></strong> 
         <em><?php include_partial("student/is_registered", array("student" => $student)) ?></em>
   </div>
    <div class="info_div"><?php include_partial("student/careers", array("student" => $student)) ?></div>
    <div class="info_div"><?php include_partial("student/divisions", array("student" => $student)) ?></div>
    <div class="info_div"><?php include_partial("student/commisions", array("student" => $student)) ?></div>
    <div class="info_div"><?php include_partial("student/disciplinary_sanctions", array("student" => $student)); ?></div>
    <div class="info_div"><?php include_partial("student/absences", array("student" => $student)); ?></div>
    
  </div>
</div>
