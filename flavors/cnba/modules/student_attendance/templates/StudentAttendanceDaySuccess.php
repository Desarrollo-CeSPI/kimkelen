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
    <?php echo __('Load attendances day') ?>
  </h1>
  <div id="sf_admin_content">
    <form action="<?php echo url_for('student_attendance/SaveStudentAttendanceDay') ?>" method="post" >
      <ul class="sf_admin_actions">
        <li class ="sf_admin_action_list" ><?php echo link_to(__('Back'), 'student_attendance/SelectValuesForAttendanceDay'); ?></li>
        <li ><input type="submit" value="<?php echo __('Save', array(), 'sf_admin') ?>" /></li>
      </ul>

      <?php if (!$sf_user->isPreceptor()): ?>
        <div class="week_move">
          <?php echo image_tag('../sfPropelPlugin/images/previous.png') ?>
          <?php echo link_to(__('previous week'), 'student_attendance/StudentAttendanceDay', array('query_string' => "year=$year&career_school_year_id=$career_school_year_id&division_id=$division_id&day=" . date('d/m/Y', strtotime($day . '- 1 week')))); ?>
          <?php echo link_to(__('next week'), 'student_attendance/StudentAttendanceDay', array('query_string' => "year=$year&career_school_year_id=$career_school_year_id&division_id=$division_id&day=" . date('d/m/Y', strtotime($day . '+ 1 week')))); ?>
          <?php echo image_tag('../sfPropelPlugin/images/next.png') ?>
        </div>

      <?php endif; ?>
      <?php echo strtr($form->getWidgetSchema()->getFormFormatter()->getDecoratorFormat(), array("%content%" => (string) $form)) ?>

      <?php if (!$sf_user->isPreceptor()): ?>
        <div class="week_move">
          <?php echo image_tag('../sfPropelPlugin/images/previous.png') ?>
          <?php echo link_to(__('previous week'), 'student_attendance/StudentAttendanceDay', array('query_string' => "year=$year&career_school_year_id=$career_school_year_id&division_id=$division_id&day=" . date('d/m/Y', strtotime($day . '- 1 week')))); ?>
          <?php echo link_to(__('next week'), 'student_attendance/StudentAttendanceDay', array('query_string' => "year=$year&career_school_year_id=$career_school_year_id&division_id=$division_id&day=" . date('d/m/Y', strtotime($day . '+ 1 week')))); ?>
          <?php echo image_tag('../sfPropelPlugin/images/next.png') ?>
        </div>
      <?php endif; ?>
    </form>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>