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
<?php use_stylesheet('home-frontend') ?>

<div class="home-menu">
  <h1><?php echo __('Bachillerato de Bellas Artes') ?></h1>

  <div class="container">
    <?php echo link_to(__('Mis Calificaciones'), '@career_student', array('class' => 'button')) ?>

    <?php echo link_to(__('Mis Materias'), '@course_subject_student', array('class' => 'button')) ?>

    <?php if (pmConfiguration::getInstance()->isEnabled('absence_per_day') && pmConfiguration::getInstance()->isEnabled('absence_per_subject')): ?>
      <?php echo link_to(__('Mis Inasistencias'), '@absences', array('class' => 'button')) ?>
    <?php endif ?>

    <?php if (pmConfiguration::getInstance()->isEnabled('disciplinarysanction')): ?>
      <?php echo link_to(__('Mis Sanciones disciplinarias'), '@disciplinary_sanction', array('class' => 'button')) ?>
    <?php endif ?>

    <?php echo link_to(__('Mis Datos Personales'), '@user_information', array('class' => 'button')) ?>

    <div class="clear"></div>
  </div>

</div>