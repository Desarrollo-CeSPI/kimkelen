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
<?php include_javascripts_for_form($form) ?>

<div id="sf_admin_container">
  <h1><?php echo __('Justification')?></h1>

  <div id="sf_admin_content">
    <form action="<?php echo url_for("attendance_justification/saveJustification") ?>" method="post" enctype="multipart/form-data">
      <?php foreach ($student_attendances as $student_attendance):?>
        <input type="hidden" value="<?php echo $student_attendance?>" name="ids[]">
      <?php endforeach?>
      <fieldset>
          <?php echo $form?>
      </fieldset>
      <ul class="sf_admin_actions">
        <li class="sf_admin_action_go_back"> <?php echo link_to(__('Back'), 'attendance_justification')?></li>
        <?php if($student_attendance_justification->canDelete() && $sf_user->hasCredential('edit_attendance_justification')):?>
          <li class="sf_admin_action_delete"> <?php echo link_to(__('Delete'), 'attendance_justification/delete?id=' . $student_attendance_justification->getId(), array('confirm' => 'Are you sure?'))?></li>
        <?php endif?>
        <li><input type="submit" value="<?php echo __('Save', array(),'sf_admin') ?>" /></li>
      </ul>
    </form>
  </div>
</div>