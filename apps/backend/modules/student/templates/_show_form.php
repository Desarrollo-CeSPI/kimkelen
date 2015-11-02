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
  <?php include_partial('student/show_form_actions', array('student' => $student, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>

  <h2><?php echo __('Alumno') ?></h2>
  <?php slot('sf_admin.current_show_student_show_tabs') ?>
    <?php echo get_partial('student/student_show_tabs', array('type' => 'list', 'student' => $student)) ?>
  <?php end_slot(); ?>
  <?php include_slot('sf_admin.current_show_student_show_tabs') ?>

  <?php include_partial('student/show_form_actions', array('student' => $student, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
</div>
