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
  <h1><?php echo __('Course configuration of the %course%', array('%course%' => $course->__toString())) ?></h1>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('shared_course/updateCourseConfiguration') ?>" method="post">

      <input type="hidden" name="id" value="<?php echo $course->getId() ?>" />

      <div>
        <h2><?php echo __('Absences by period') ?></h2>
        <?php $first = true ?>
        <?php foreach ($course_subjects as $course_subject): ?>
          <?php if ($course_subject->getCareerSubject()->getHasOptions()): ?>
            <?php continue ?>
          <?php endif; ?>

          <a class="tab<?php $first and print ' tab-selected' ?>" href="#course_subject_configuration_fieldset_<?php echo $course_subject->getId() ?>" onclick="jQuery('fieldset').hide(); jQuery(jQuery(this).attr('href')).show(); jQuery('.tab').removeClass('tab-selected'); jQuery(this).addClass('tab-selected'); return false;"><?php echo $course_subject->getCareerSubject() ?></a>
          <?php $first = false ?>
        <?php endforeach; ?>
      </div>

      <?php foreach ($course_subjects as $course_subject): ?>
        <?php if ($course_subject->getCareerSubject()->getHasOptions()): ?>
          <?php continue ?>
        <?php endif; ?>

        <fieldset id="course_sbuject_configuration_fieldset_<?php echo $course_subject->getId() ?>" class="course_sbuject_configuration_fieldset">
          <h2><?php echo $course_subject->getCareerSubject() ?></h2>
          <?php echo $forms[$course_subject->getId()] ?>
        </fieldset>
      <?php endforeach; ?>

      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), "@$referer_module", array('class' => 'sf_admin_action_go_back')) ?></li>

        <?php if ($is_bimester): ?>
        <li><?php echo link_to(__('Delete previous configuration'), "@delete_course_configuration", array('class' => 'sf_admin_action_course_delete_configuration')) ?></li>
      <?php endif; ?>
        <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
      </ul>
    </form>
  </div>
  <div style="margin-top: 1px; clear: both;">
  </div>
</div>