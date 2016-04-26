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
<?php include_partial('student/assets') ?>
<?php include_javascripts_for_form($form) ?>

<div id="sf_admin_container">
  <h1><?php echo __('Change %student% status', array("%student%" => $student)) ?></h1>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('student/updateStudentStatus') ?>" method="post">

      <input type="hidden" name="student_id" value="<?php echo $student->getId() ?>" />
      <fieldset>
        <?php  echo $form ?>
      </fieldset>


      <ul class="sf_admin_actions">
        <?php echo $helper->linkToList(array('label' => __('Go back'), 'params' => array(), 'class_suffix' => 'list',)) ?>
        <?php echo $helper->linkToSave($form->getObject(), array('params' => array(), 'class_suffix' => 'save_and_list', 'label' => __('Save'),)) ?>
      </ul>
      
      <div class="warning">
		  <?php echo __('El estado Retirado de la institución lo desmatriculará y desactivará.') ?>
		  <?php echo __('El estado Retirado de la institución con reserva de banco lo desmatriculará.') ?>
	  </div>
    </form>
  </div>
</div>
