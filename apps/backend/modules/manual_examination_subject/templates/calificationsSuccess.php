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

<?php foreach ($forms as $form): ?>
  <?php include_stylesheets_for_form($form) ?>
  <?php include_javascripts_for_form($form) ?>
<?php endforeach ?>

<div id="sf_admin_container">
  <h1><?php echo __('%examination_subject% califications', array('%examination_subject%' => $examination_subject->getCareerSubjectSchoolYear()->getCareerSubject())) ?></h1>
  <div class="examination">
    <h3><?php echo __('Examination %examination%', array('%examination%' => $examination_subject->getExamination())) ?></h3>
    <h3><?php echo __('School year %%school_year%%', array('%%school_year%%' => $examination_subject->getCareerSubjectSchoolYear()->getSchoolYear())) ?></h3>
  </div>
  <div id="sf_admin_content">
    <form action="<?php echo url_for('manual_examination_subject/califications') ?>" method="post">
      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), '@manual_examination_subject', array('class' => 'sf_admin_action_go_back')) ?></li>
        <?php if (!$examination_subject->getIsClosed()): ?>
          <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
        <?php endif ?>
      </ul>
      <input type="hidden" id="id" name="id" value="<?php echo $examination_subject->getId() ?>"/>

      <fieldset id="califications_fieldset">
        <?php foreach ($forms as $form): ?>
          <?php echo $form->renderHiddenFields() ?>
          <div class="sf_admin_form_row">
            <div style="min-height: 75px;"> 
              <?php if ($form->hasGlobalErrors()): ?>
                <?php echo $form->renderGlobalErrors() ?>
              <?php endif ?>
              <?php echo $form["id"] ?>
              <?php echo $form["mark"]->renderError() ?>
              <?php echo $form["mark"]->renderLabel() ?>
              <?php echo 'Nota: ' . $form["mark"] ?>
              <?php if (isset($form["is_absent"])): ?>
                <?php echo $form["is_absent"] ?><span style="margin-left: 10px"><?php echo __("Is absent") ?>?</span>
              <?php endif ?>
              <div class="help">
                <?php echo $form["mark"]->renderHelp() ?>
              </div>
            </div>
            <?php include_component('course_student_mark', 'component_marks_info', array('course_subject_student' => $form->getObject()->getCourseSubjectStudent())) ?>
          </div>
        <?php endforeach ?>
      </fieldset>

      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), '@manual_examination_subject', array('class' => 'sf_admin_action_go_back')) ?></li>
        <?php if ($examination_subject->canEditCalifications()): ?>
          <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
        <?php endif ?>
      </ul>
    </form>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>