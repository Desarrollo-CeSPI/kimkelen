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
<?php use_helper('I18N', 'Javascript') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>

<div id="sf_admin_container">
  <h1>
    <?php echo __('Student reports') ?>
  </h1>
  <div id="sf_admin_content">
    <h2><?php echo __('School year') . ' ' . $school_year ?></h2>

    <div class="sf_admin_list">
      <div id="general">
        <h1><?php echo __('General'); ?></h1>
        <table>
          <thead>
            <tr>
              <?php include_partial('th_student_report', array('no_details' => true)) ?>
            </tr>
          </thead>
          <tbody>
            <?php include_partial('tr_student_report', array('report_array' => $stats_table['student_reports'], 'no_details' => true)); ?>
          </tbody>
        </table>
      </div>

      <br/><br/>
      <div id="years">
        <h1><?php echo __('Year'); ?></h1>
        <table>
          <thead>
            <tr>
              <?php include_partial('th_student_report', array('no_details' => true)) ?>
            </tr>
          </thead>
          <tbody>
            <?php include_partial('tr_student_report', array('report_array' => $stats_table['year_reports'], 'no_details' => true)); ?>
          </tbody>
        </table>
      </div>
      
      <br/><br/>
      <div id="shifts">
        <h1><?php echo __('Shifts'); ?></h1>
        <table>
          <thead>
            <tr>
              <?php include_partial('th_student_report', array('no_details' => true)) ?>
            </tr>
          </thead>
          <tbody>
            <?php include_partial('tr_student_report', array('report_array' => $stats_table['shift_reports'], 'no_details' => true)); ?>
          </tbody>
        </table>
      </div>

      <br/><br/>
      <div id="shift_divisions">
        <h1><?php echo __('Shift division'); ?></h1>
        <table>
          <thead>
            <tr>
              <?php include_partial('th_student_report', array('no_details' => true)) ?>
            </tr>
          </thead>
          <tbody>
            <?php include_partial('tr_student_report', array('report_array' => $stats_table['shift_division_reports'], 'no_details' => true)); ?>
          </tbody>
        </table>
      </div>

      <br/><br/>
      <div id="year_shifts">
        <h1><?php echo __('Year shift'); ?></h1>
        <table>
          <thead>
            <tr>
              <?php include_partial('th_student_report', array('no_details' => true)) ?>
            </tr>
          </thead>
          <tbody>
            <?php include_partial('tr_student_report', array('report_array' => $stats_table['year_shift_reports'], 'no_details' => true)); ?>
          </tbody>
        </table>
      </div>

      <br/><br/>
      <div id="year_shift_divisions">
        <h1><?php echo __('Year shift and division'); ?></h1>
        <table>
          <thead>
            <tr>
              <?php include_partial('th_student_report', array('no_details' => true)) ?>
            </tr>
          </thead>
          <tbody>
            <?php include_partial('tr_student_report', array('report_array' => $stats_table['year_shift_division_reports'], 'no_details' => true)); ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>