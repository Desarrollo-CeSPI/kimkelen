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
<?php use_helper('Javascript', 'Object','I18N','Form', 'Asset') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>


<div id="sf_admin_container">
  <h1><?php echo __('%examination_subject% students', array('%examination_subject%' => $examination_subject->getCareerSubjectSchoolYear()->getCareerSubject())) ?></h1>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('manual_examination_subject/manageStudents') ?>" method="post">
      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), '@manual_examination_subject', array('class' => 'sf_admin_action_go_back')) ?></li>
        <?php if (!$examination_subject->getIsClosed()): ?>
          <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
        <?php endif ?>
      </ul>
      <input type="hidden" id="id" name="id" value="<?php echo $examination_subject->getId() ?>"/>

      <fieldset>
        <?php echo $form?>
      </fieldset>

      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), '@manual_examination_subject', array('class' => 'sf_admin_action_go_back')) ?></li>
        <?php if (!$examination_subject->getIsClosed()): ?>
          <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
        <?php endif ?>
      </ul>
    </form>
  </div>
  <div style="margin-top: 1px; clear: both;">
  </div>
</div>