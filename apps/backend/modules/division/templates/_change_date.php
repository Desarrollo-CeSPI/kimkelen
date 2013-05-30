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
<?php use_helper('Javascript', 'Object','I18N','Form', 'Asset') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>

<?php $form = new SchoolYearAttendanceDayDateForm();?>

<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<div id="sf_admin_container">
  <div id="sf_admin_content">
    <form action="<?php echo url_for('@change_school_year_attendance_date') ?>" method="post">

      <?php echo $form->renderHiddenFields();?>

      <?php echo input_hidden_tag('division_id',$division_id);?>

      <div style="float: left">
        <table>
          <?php echo $form;?>
        </table>
      </div>

      <div style="float: left; padding: 1%">
        <input type="submit" value="<?php echo __('change date') ?>" />
      </div>


    </form>
  </div>
  <div style="margin-top: 1px; clear: both;">
  </div>
</div>