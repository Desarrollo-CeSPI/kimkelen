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
<?php use_helper('Javascript', 'Object', 'I18N', 'Form', 'Asset') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php include_stylesheets_for_form($form) ?>

<div id="sf_admin_container">
  <?php if (isset($division)): ?>
    <h1><?php echo __('Attendance sheet for division: %division%', array('%division%' => $division)) ?></h1>
  <?php else: ?>
    <h1><?php echo __('Attendance sheet for course subject: %course_subject%', array('%course_subject%' => $course)) ?></h1>
  <?php endif; ?>
  <div id="sf_admin_content">
    <fieldset>
      <h2>  <?php echo __('Please select a date range from below and press the show button.'); ?>  </h2>
      <form action="<?php echo url_for($url) ?>" method="post">
        <?php echo $form->renderHiddenFields(); ?>
        <div>
          <table>
            <?php echo $form; ?>
          </table>
        </div>
        <div class="attendance_sheet_footer">
          <input type="submit" value="<?php echo __('Show attendances') ?>" />
        <ul id="go_back_action" class="sf_admin_actions">
          <li><?php echo link_to(__('Volver al listado'), $module, array('class' => 'sf_admin_action_go_back')) ?></li>
        </ul>
        </div>
      </form>
    </fieldset>
  </div>
</div>