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
<?php include_partial('attendance_justification/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Justification')?></h1>

  <div id="sf_admin_content">    
      <fieldset>
        <div class="sf_admin_form_row">
          <div>
            <label><?php echo __('Student')?></label>
          </div>
          <?php echo $student_attendance_justification->getStudent()?>
          
        </div>
        <div class="sf_admin_form_row">
          <div>
            <label><?php echo __('Days')?></label>
          </div>
          <?php echo $student_attendance_justification->getJustifiedDays()?>
          
        </div>
        <div class="sf_admin_form_row">
          <div>
            <label><?php echo __('Justification type')?></label>
          </div>
          <?php echo $student_attendance_justification->getJustificationType()?>
          
        </div>
        <div class="sf_admin_form_row">
          <div>
            <label><?php echo __('Observation')?></label>
          </div>
          <?php echo $student_attendance_justification->getObservation()?>
          
        </div>
        <div class="sf_admin_form_row">
          <div>
            <label><?php echo __('Document')?></label>
          </div>
          <?php if ($student_attendance_justification->getDocument()):?>
            <?php echo link_to(__('Download Document'), 'mainBackend/downloableDocument?id='.$student_attendance_justification->getId()); ?>
          <?php else:?>
            <?php echo __('Dont have any documentation')?>
          <?php endif?>
          
        </div>
      </fieldset>
      <ul class="sf_admin_actions">
        <li class="sf_admin_action_go_back"> <?php echo link_to(__('Back'), 'attendance_justification')?></li>
        <?php if($student_attendance_justification->canDelete() && $sf_user->hasCredential('edit_attendance_justification')):?>
          <li class="sf_admin_action_delete"> <?php echo link_to(__('Delete'), 'attendance_justification/delete?id=' . $student_attendance_justification->getId(), array('confirm' => 'Are you sure?'))?></li>
        <?php endif?>        
      </ul>    
  </div>
</div>