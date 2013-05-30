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
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<div class="sf_admin_form">
  <ul class="sf_admin_actions">
    <li><?php echo link_to(__('Back to divisions'), '@division_course', array('class' => 'sf_admin_action_go_back')) ?></li>
  </ul>

  <?php include_partial('shared_course/show_form_actions', array('course' => $course, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>

  <h2><?php echo __('Course') ?></h2>
  <?php slot('sf_admin.current_show_course_show_tabs') ?>
  <?php echo get_partial('shared_course/course_show_tabs', array('type' => 'list', 'course' => $course)) ?>
  <?php end_slot(); ?>
  <?php include_slot('sf_admin.current_show_course_show_tabs') ?>


  <div class="sf_admin_form">
    <ul class="sf_admin_actions">
      <li><?php echo link_to(__('Back to divisions'), '@division_course', array('class' => 'sf_admin_action_go_back')) ?></li>
    </ul>
    <?php include_partial('shared_course/show_form_actions', array('course' => $course, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </div>