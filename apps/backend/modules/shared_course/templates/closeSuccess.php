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
<?php use_helper('I18N') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<div id="sf_admin_container">
  <?php if (!$course->getIsClosed()): ?>
    <h1><?php echo __('Close Course %course%', array('%course%' => $course))?></h1>
  <?php else: ?>
    <h1><?php echo __("%course% califications", array("%course%" => $course)) ?></h1>
  <?php endif ?>
  <div id="sf_admin_content">
    <form action="<?php echo url_for('shared_course/saveClose') ?>" method="post">
      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), "@$referer_module", array('class' => 'sf_admin_action_go_back')) ?></li>
        <?php if (!$course->getIsClosed()): ?>
          <li><input type="submit" value="<?php echo __('Confirm') ?>" onCLick="return (confirm('¿Esta seguro?'));" /></li>
        <?php endif ?>
      </ul>
      <input type='hidden' id="id" name="id" value="<?php echo $course->getId()?>">



      <?php foreach ($course->getNonOptionalCourseSubjects() as $course_subject):?>
        <?php if ($course_subject->isNotAverageable()):?>
         lclc
          <?php else: ?>
          <?php include_partial('shared_course/course_subject_students', array('course_subject' => $course_subject))?>
          <?php endif; ?>
        <?php endforeach ?>
      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), "@$referer_module", array('class' => 'sf_admin_action_go_back')) ?></li>
        <?php if (!$course->getIsClosed()): ?>
          <li><input type="submit" value="<?php echo __('Confirm') ?>" onCLick="return (confirm('¿Esta seguro?'));" /></li>
        <?php endif ?>
      </ul>
    </form>
  </div>
  <div style="margin-top: 1px; clear: both;">
  </div>
</div>