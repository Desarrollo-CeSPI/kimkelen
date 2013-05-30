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
<?php use_helper('I18N', 'Date') ?>
<?php include_partial('student_reincorporation/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Edit reincorporation for %student%', array("%student"=> $student)) ?></h1>

  <?php include_partial('student_reincorporation/flashes') ?>

  <?php include_partial('student_reincorporation/form_slot_actions', array('student_reincorporation' => $student_reincorporation, 'form' => $form, 'helper' => $helper)) ?>

  <div id="sf_admin_header">
    <?php include_partial('student_reincorporation/form_header', array('student_reincorporation' => $student_reincorporation, 'form' => $form, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('student_reincorporation/form', array('student_reincorporation' => $student_reincorporation, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('student_reincorporation/form_footer', array('student_reincorporation' => $student_reincorporation, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </div>
</div>