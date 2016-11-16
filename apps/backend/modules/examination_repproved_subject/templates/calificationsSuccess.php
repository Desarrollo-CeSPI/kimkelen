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
  <h1><?php echo __('%examination_repproved_subject% califications', array('%examination_repproved_subject%' => $examination_repproved_subject->getCareerSubject())) ?></h1>
  <div class="examination">
    <h3><?php echo __('Examination %examination%', array('%examination%' => $examination_repproved_subject->getExaminationRepproved())) ?></h3>
    <h3><?php echo __('School year %%school_year%%', array('%%school_year%%' => $examination_repproved_subject->getExaminationRepproved()->getSchoolYear())) ?></h3>
  </div>
  <div id="sf_admin_content">
    <form action="<?php echo url_for('examination_repproved_subject/updateCalifications') ?>" method="post">
      <ul class="sf_admin_actions">
        <li>
          <?php echo link_to(__('Back'), '@examination_repproved_subject', array('class' => 'sf_admin_action_go_back')) ?></li>
          <?php if (!$examination_repproved_subject->getIsClosed()): ?>
            <li>
              <input type="submit" value="<?php echo __('Save') ?>" />
            </li>
          <?php endif?>
      </ul>

      <input type="hidden" id="id" name="id" value="<?php echo $examination_repproved_subject->getId() ?>"/>

      <fieldset id="califications_fieldset">
        <?php foreach ($forms as $form): ?>
          <div class="sf_admin_form_row">
            <?php if ($form->hasGlobalErrors()): ?>
              <?php echo $form->renderGlobalErrors() ?>
            <?php endif ?>
            <?php echo $form->renderHiddenFields() ?>
            <?php echo $form["id"] ?>
            <?php echo $form["mark"]->renderError() ?>
            <?php echo $form["mark"]->renderLabel() ?><?php echo $form["mark"] ?>
            <?php if (isset($form["is_absent"])): ?>
              <?php echo $form["is_absent"] ?><span style="margin-left: 10px"><?php echo __("Is absent") ?>?</span>
            <?php endif ?>
            <div class="help">
              <?php echo $form["mark"]->renderHelp() ?>
            </div>

	          <?php $course_subject_student = $form->getObject()->getStudentRepprovedCourseSubject()->getCourseSubjectStudent(); ?>
	          <div class="ows">
		          <?php echo $course_subject_student->getStudent()->owsCorrelativeFor($examination_repproved_subject->getCareerSubject()) ? " (" . __('Ows correlative') . ")": ""; ?>
	          </div>

            <div style="clear: both; margin-top: 1px;"></div>
            <?php include_component('course_student_mark', 'component_marks_info', array('course_subject_student' => $course_subject_student)) ?>
          </div>

        <?php endforeach ?>
      </fieldset>

      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), '@examination_repproved_subject', array('class' => 'sf_admin_action_go_back')) ?></li>
        <?php if ($examination_repproved_subject->canEditCalifications()): ?>
          <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
        <?php endif?>
      </ul>
    </form>
  </div>
  <div style="margin-top: 1px; clear: both;">
  </div>
</div>