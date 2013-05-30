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
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>

<?php $form = new SchoolYearAttendanceDayDateForm();?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<div id="sf_admin_container">
  <h1><?php echo __('Cambiar Fecha:') ?></h1>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('division/updateSchoolYearAttendanceDate') ?>" method="post">

      <?php echo $form->renderHiddenFields();?>

      <?php #echo input_hidden_tag('division_id',$division->getId());?>

      <fieldset>
        <h2><?php echo __('Pickup the date.');?></h2>        
          <?php echo $form;?>        
      </fieldset>

      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Volver al listado de divisiones'), '@division', array('class' => 'sf_admin_action_go_back')) ?></li>
        <li><input type="submit" value="<?php echo __('Ok') ?>" /></li>
      </ul>
    </form>
  </div>
  <div style="margin-top: 1px; clear: both;">
  </div>
</div>