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
    <?php if (!isset($career_school_year)): $career_school_year=""; endif;?>
    <?php include_partial('student_reports_header', array('school_year' =>$school_year, 'career_school_year'=>$career_school_year, 'shift'=>$shift, 'year'=>$year, 'division'=>$division)) ?>
    <?php include_partial('report_tabs') ?>

    <div class="sf_admin_list">
      <div id="general" class="tab_content">
        <table>
          <thead>
            <tr>
              <?php include_partial('th_student_report') ?>
            </tr>
          </thead>
          <tbody>
            <?php include_partial('tr_student_report', array('report_array' => $stats_table['student_reports'])); ?>
          </tbody>
        </table>
      </div>

      <div id="shifts" class="tab_content">
        <table>
          <thead>
            <tr>
              <?php include_partial('th_student_report') ?>
            </tr>
          </thead>
          <tbody>
            <?php include_partial('tr_student_report', array('report_array' => $stats_table['shift_reports'])); ?>
          </tbody>
        </table>
      </div>

      <div id="shift_divisions" class="tab_content">
        <table>
          <thead>
            <tr>
              <?php include_partial('th_student_report') ?>
            </tr>
          </thead>
          <tbody>
            <?php include_partial('tr_student_report', array('report_array' => $stats_table['shift_division_reports'])); ?>
          </tbody>
        </table>
      </div>

      <div id="years" class="tab_content">
        <table>
          <thead>
            <tr>
              <?php include_partial('th_student_report') ?>
            </tr>
          </thead>
          <tbody>
            <?php include_partial('tr_student_report', array('report_array' => $stats_table['year_reports'])); ?>
          </tbody>
        </table>
      </div>

      <div id="year_shifts" class="tab_content">
        <table>
          <thead>
            <tr>
              <?php include_partial('th_student_report') ?>
            </tr>
          </thead>
          <tbody>
            <?php include_partial('tr_student_report', array('report_array' => $stats_table['year_shift_reports'])); ?>
          </tbody>
        </table>
      </div>

      <div id="year_shift_divisions" class="tab_content">
        <table>
          <thead>
            <tr>
              <?php include_partial('th_student_report') ?>
            </tr>
          </thead>
          <tbody>
            <?php include_partial('tr_student_report', array('report_array' => $stats_table['year_shift_division_reports'])); ?>
          </tbody>
        </table>
      </div>
    </div>
    <ul class="sf_admin_actions">
      <li class="sf_admin_action_list">
        <?php echo link_to(__("Back"), 'student_stats/filterForStudentStats') ?>
      </li>
      <li class="sf_admin_action_print_students">
        <?php $parameters = ""; ?>
        <?php foreach ($params as $key => $param): ?>
          <?php $parameters .= $key . '=' . $param . '&'; ?>
        <?php endforeach; ?>
        <?php $parameters = substr($parameters, 0, -1); ?>
        <?php echo link_to(__("Export report cards"), '@export_student_stats?'.$parameters, array('target' => '_blank')) ?>
      </li>
    </ul>
  </div>
</div>