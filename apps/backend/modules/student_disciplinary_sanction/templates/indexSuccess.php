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
<?php use_helper('I18N', 'Date', 'Javascript') ?>
<?php include_partial('student_disciplinary_sanction/assets') ?>

<div id="sf_admin_container">

  <h1><?php echo __('Listado de sanciones del alumno %%student%%', array('%%student%%' => $student), 'messages') ?></h1>

  <?php include_partial('student_disciplinary_sanction/list_slot_actions', array('helper' => $helper)) ?>

  <div id="sf_admin_header">
    <?php include_partial('student_disciplinary_sanction/list_header', array('pager' => $pager)) ?>
  </div>

  <?php if ($configuration->hasFilterForm()): ?>
  <div align="center">
    <div id="sf_admin_bar">
      <?php include_partial('student_disciplinary_sanction/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
    </div>
  </div>
  <?php endif ?>

  <?php include_partial('student_disciplinary_sanction/flashes') ?>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('student_disciplinary_sanction/allBatch', array('action' => 'batch')) ?>" method="post">
    <ul class="sf_admin_actions">
      <?php include_partial('student_disciplinary_sanction/list_all_batch_actions', array('helper' => $helper)) ?>
    </ul>
    </form>

    <form action="<?php echo url_for('student_disciplinary_sanction_collection', array('action' => 'batch')) ?>" method="post">
    <ul class="sf_admin_actions">
      <input type="hidden" id="batch_action" name="batch_action">
      <?php include_partial('student_disciplinary_sanction/list_batch_actions', array('helper' => $helper, 'select_id' => 'top')) ?>
      <?php include_partial('student_disciplinary_sanction/list_actions', array('helper' => $helper)) ?>
    </ul>
    <?php include_partial('student_disciplinary_sanction/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('student_disciplinary_sanction/list_batch_actions', array('helper' => $helper, 'select_id' => 'bottom')) ?>
      <?php include_partial('student_disciplinary_sanction/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
    <form action="<?php echo url_for('student_disciplinary_sanction/allBatch', array('action' => 'batch')) ?>" method="post">
    <ul class="sf_admin_actions">
      <?php include_partial('student_disciplinary_sanction/list_all_batch_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('student_disciplinary_sanction/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
<?php if ($configuration->isExportationEnabled()): ?>
  <?php javascript_tag() ?>
    jQuery(window).bind('resize', function() {
      jQuery('#sf_admin_exportation').centerHorizontally();
      jQuery('#sf_admin_exportation_resizable_area').ensureVisibleHeight();
    });
  <?php end_javascript_tag() ?>
<?php endif ?>