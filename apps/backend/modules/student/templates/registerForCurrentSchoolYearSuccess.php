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
  <h1><?php echo __('School year register') ?></h1>

  <div id="sf_admin_content">
    <h2><?php echo $form->getObject()->isNew() ? __("Register %student% in current school year", array("%student%" => $student)) : __("Current school year registration status for %student%", array("%student%" => $student)) ?></h2>
    <form action="<?php echo url_for('student/updateRegistrationForCurrentSchoolYear') ?>" method="post">

      <input type="hidden" name="student_id" value="<?php echo $student->getId() ?>" />
      <fieldset>
        <?php  echo $form ?>
      </fieldset>

      <?php if (!$form->getObject()->isNew() && $student->getCurrentDIvisions()): ?>
        <br>
        <div class="warning"><?php echo __('Recuerde eliminar al alumno de la división actual si va a desmatricularlo del año lectivo.') ?></div>
      <?php endif; ?>
      <ul class="sf_admin_actions">
        <?php echo $helper->linkToList(array('label' => __('Go back'), 'params' => array(), 'class_suffix' => 'list',)) ?>
        <li class="sf_admin_action_delete"> <?php echo link_to(__('Change status'), 'student/changeStudentStatus?id='. $student->getId()) ?> </li>
        <?php echo $helper->linkToSave($form->getObject(), array('params' => array(), 'class_suffix' => 'save_and_list', 'label' => __('Save'),)) ?>
      </ul>
    </form>
  </div>
</div>
