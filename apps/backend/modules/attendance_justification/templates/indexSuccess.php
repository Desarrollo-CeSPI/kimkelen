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
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<div id="sf_admin_container">
  <h1>
    <?php echo __('Attendance justification') ?>
  </h1>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('@attendance_justification')?>" method="post" >
      <?php echo $form->renderHiddenFields()?>
      <?php if ($form->hasGlobalErrors()): ?>
        <?php echo $form->renderGlobalErrors() ?>
      <?php endif ?>

      <fieldset>
        <div class="sf_admin_form_row">
          <?php echo $form['from_date']->renderRow()?>
        </div>
        <div class="sf_admin_form_row">
          <?php echo $form['to_date']->renderRow()?>
        </div>
        <div class="sf_admin_form_row">
          <?php echo $form['student']->renderRow()?>
			<div class="help">
				Filtra por apellido o por número de documento
		    </div>
        </div> 
        <?php if ($has_subject_attendance): ?>
            <div class="sf_admin_form_row">
                 <?php echo $form['attendance_subject']->renderRow()?>
            </div>
        <?php endif ?>
        <ul class="sf_admin_actions">
          <li><input type="submit" value="<?php echo __('Filter', array(),'sf_admin') ?>" /></li>
        </ul
    </form>

    <form action="<?php echo url_for('@attendance_justification_justificate')?>" method="post" >
        <?php if (isset($student_attendances) && (count($student_attendances))):?>
          <?php include_partial('attendance_justification/information_box')?>

        <?php if ($has_subject_attendance): ?>
                  <?php include_partial('attendance_justification/subject', array('student_attendances'=>$student_attendances))?>
        <?php  else :?>
                  <?php include_partial('attendance_justification/day', array('student_attendances'=>$student_attendances))?>
        <?php endif ?>



        <ul class="sf_admin_actions">
          <li>
            <input type="submit" value="<?php echo __('Justificación multiple', array(),'sf_admin') ?>" />
            <div class="justification_help"><?php echo __('Can do multiple justification, with only a student.')?></div>
          </li>
        </ul>
        <?php else: ?>
          <div class='no_results_advice'><?php echo __('No se encontraron resultados en la búsqueda.'); ?></div>
        <?php endif?>
      </fieldset>
    </form>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
