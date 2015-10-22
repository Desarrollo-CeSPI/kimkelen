<?php /*
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
<?php use_helper('Object', 'I18N', 'Form', 'Date') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php #use_stylesheet('fix/jquery.dataTables.css')           ?>
<?php use_javascript('fix/jquery.dataTables.js') ?>
<?php use_javascript('fix/FixedColumns.js') ?>
<?php use_stylesheet('print-attendance-template.css', 'last', array('media' => 'print')) ?>


<div id="sf_admin_container">
  <div class="non-printable">
    <ul class="sf_admin_actions">
      <li><a href="javascript:history.go(-1)" class="sf_admin_action_go_back"><?php echo __('Back') ?></a></li>
      <li><a href="#" onclick="window.print(); return false;" class="sf_admin_action_print"><?php echo __('Print') ?></a></li>
    </ul>
  </div>

  <?php if ($course_subject == null) : ?>
    <h1 class="print-title"><?php echo __('Attendance sheet for division: %division%', array('%division%' => $division)) ?></h1>
  <?php else: ?>
    <h1 class="print-title"><?php echo __('Asistencias a la materia: %subject%', array('%subject%' => $course_subject)) ?></h1>
  <?php endif; ?>
  
  <div class="non-printable" style="margin-bottom: 10px;">
    <div>
      <?php echo image_tag('../sfPropelPlugin/images/previous.png') ?>
      <?php echo link_to(__('Previous month'), 'student_attendance/PrintAttendanceTemplate', array('query_string' => "url=". $url ."&id=". $id ."&month=". $prevMonth ."")); ?>
      <?php echo link_to(__('Next month'), 'student_attendance/PrintAttendanceTemplate', array('query_string' => "url=". $url ."&id=". $id ."&month=". $nextMonth ."")); ?>
      <?php echo image_tag('../sfPropelPlugin/images/next.png') ?>
    </div>
  </div>

  <div id="sf_admin_content">
      <table id="miTabla" class="print_table" cellspacing="0px;">
        <thead>
          <tr>
            <th><?php echo __('Students'); ?></th>
            <?php foreach ($days as $day): ?>
              <th class="attendance_day"><span class="date_first"><?php echo date('d', $day); ?></span><span class="date_separator">/</span><span class="date_last"><?php echo date('m', $day); ?></span></th>
            <?php endforeach; ?>
            <th class="attendance_day"><?php echo __('Total'); ?></th>
            <th class="attendance_day"><?php echo __('Total absences till today (without justification)'); ?></th>
            <th class="attendance_day"><?php echo __('Total absences till today'); ?></th>
          </tr>
        </thead>
        <tbody>

          <?php foreach ($students as $student) : ?>
          <tr>
            <th class="student_fix" align='left'><?php echo $student ?></th>
            <?php foreach ($days as $day): ?>
              <td style="border: 1px solid #555"></td>
            <?php endforeach; ?>  
            <td style="border: 1px solid #555"></td>
            <td style="border: 1px solid #555"></td>
            <td style="border: 1px solid #555"></td>
          <?php endforeach; ?>
          </tr>
        </tbody>
      </table>
  </div>
</div>
