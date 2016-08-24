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
<?php use_helper('Javascript', 'Object', 'I18N', 'Form', 'Asset') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php foreach ($forms as $form): ?>
  <?php include_stylesheets_for_form($form) ?>
  <?php include_javascripts_for_form($form) ?>
<?php endforeach ?>
<div id="sf_admin_container">
  <h1><?php echo __('Configuración de los alumnos de la comisión %course%', array('%course%' => $course)) ?></h1>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('pathway_commission/updateCourseSubjectStudents') ?>" method="post">
      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), "@pathway_commission", array('class' => 'sf_admin_action_go_back')) ?></li>
        <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
      </ul>
      <input type="hidden" id="id" name="id" value="<?php echo $course->getId() ?>"/>
      <input type="hidden" id="selected" name="selected" value="<?php echo $selected ?>"/>
      <div>
        <?php foreach ($course->getCourseSubjects() as $course_subject): ?>
          <?php if ($course_subject->getCareerSubject()->getHasOptions()): ?>
            <?php continue ?>
          <?php endif ?>

          <?php echo link_to($course_subject->getCareerSubjectToString(), "pathway_commission/courseSubjectStudent?id=" . $course->getId() . "&selected=" . $course_subject->getId(), array("confirm" => __("When you change tab, the changes not saved will be lost."), "class" => "tab" . ($course_subject->getId() == $selected ? " tab-selected" : ""))) ?>
        <?php endforeach ?>
      </div>


      <?php foreach ($course_subjects as $course_subject): ?>
        <?php if ($course_subject->getCareerSubject()->getHasOptions()): ?>
          <?php continue ?>
        <?php endif ?>


        <fieldset id="marks_fieldset_<?php echo $course_subject->getId() ?>" class="marks-fieldset"<?php if ($selected != $course_subject->getId()) echo "style=\"display: none\"" ?>>
          <h2><?php echo $course_subject->getCareerSubject() ?></h2>

          <?php echo $forms[$course_subject->getId()] ?>

        </fieldset>
      <?php endforeach ?>

      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), "@pathway_commission", array('class' => 'sf_admin_action_go_back')) ?></li>
        <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
      </ul>
    </form>
  </div>
  <div style="margin-top: 1px; clear: both;">
  </div>
</div>