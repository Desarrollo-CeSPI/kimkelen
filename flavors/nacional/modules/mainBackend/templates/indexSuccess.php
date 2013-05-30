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
<?php use_helper('Javascript') ?>
<?php use_stylesheet('home') ?>
<?php use_javascript('home') ?>

<div id="home_container">
  <h1><?php echo __('Kimkelen') ?></h1>

  <?php include_component('mainBackend', 'generalInformation') ?>

  <?php if($sf_user->hasCredential('show_student')):?>
    <?php include_component('mainBackend', 'studentBox') ?>
  <?php endif?>
  
  <?php if($sf_user->hasCredential('show_teacher')):?>
    <?php include_component('mainBackend', 'teacherBox') ?>
  <?php endif?>
  
  <?php if($sf_user->hasCredential('show_course')):?>
    <?php include_component('mainBackend', 'divisionBox') ?>
  <?php endif?>
  
</div>

<?php javascript_tag() ?>
  jQuery(initialize_home);
<?php end_javascript_tag() ?>