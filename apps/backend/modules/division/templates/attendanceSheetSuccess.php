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
<?php use_helper('Object', 'I18N', 'Form', 'Date') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php #use_stylesheet('fix/jquery.dataTables.css')          ?>
<?php use_javascript('fix/jquery.dataTables.js') ?>
<?php use_javascript('fix/FixedColumns.js') ?>

<?php $period = null; ?>
<?php $career_school_year_id = !isset($division) ? $course_subject->getCareerSubjectSchoolYear()->getCareerSchoolYearId() : $division->getCareerSchoolYearId(); ?>
<div id="sf_admin_container">

  <?php if ($course_subject == null) : ?>
    <h1><?php echo __('Attendance sheet for division: %division%', array('%division%' => $division)) ?></h1>
  <?php else: ?>
    <h1><?php echo __('Asistencias a la materia: %subject%', array('%subject%' => $course_subject)) ?></h1>
  <?php endif; ?>
  <h2><?php echo __('from date %from_date% to date %to_date%', array('%from_date%' => $from_date, '%to_date%' => $to_date)) ?></h2>
  <?php include_partial('student/information_box') ?>
  <div id="sf_admin_content">
    <div id="mi_tabla_wrapper">

      <table id="miTabla">
        <thead>
          <tr>
            <th><?php echo __('Students'); ?></th>
            <?php foreach ($days as $day): ?>
              <th class="attendance_day"><?php echo date('d/m', $day); ?></th>
            <?php endforeach; ?>
            <th class="attendance_day"><?php echo __('Total'); ?></th>
             <th class="attendance_day"><?php echo __('Total absences till today (without justification)'); ?></th>
            <th class="attendance_day"><?php echo __('Total absences till today'); ?></th>
          </tr>
        </thead>
        <tbody>

          <?php foreach ($students as $student) : ?>
            <?php $total = 0; ?>
            <tr>
              <th  class="student_fix"><?php echo $student ?></th>
              <?php foreach ($days as $day): ?>

                <?php if ($user_course_subject): ?>
                  <?php if (is_null($period)): ?>
                    <?php $period = CareerSchoolYearPeriodPeer::retrieveByDay($day, $course_subject->getCourseType()); ?>
                    <?php $clasz = (is_null($period)) ? '' : $student->getFreeClass($period) ?>
                  <?php elseif ($period->getEndAt() < date('Y-m-d', $day)): ?>
                    <?php $period = CareerSchoolYearPeriodPeer::retrieveByDay($day, $course_subject->getCourseType()); ?>
                    <?php $clasz = (is_null($period)) ? '' : $student->getFreeClass($period, $course_subject, CareerSchoolYearPeer::retrieveByPk($career_school_year_id), $division); ?>
                  <?php endif ?>
                <?php else: ?>
                  <?php if (is_null($period)): ?>
                    <?php $period = CareerSchoolYearPeriodPeer::retrieveByDay($day, $division->getCourseType()); ?>
                    <?php $clasz = (is_null($period)) ? '' : $student->getFreeClass($period, $course_subject, CareerSchoolYearPeer::retrieveByPk($career_school_year_id), $division); ?>
                  <?php elseif ($period->getEndAt() < date('Y-m-d', $day)): ?>
                    <?php $period = CareerSchoolYearPeriodPeer::retrieveByDay($day, $division->getCourseType()); ?>
                    <?php $clasz = (is_null($period)) ? '' : $student->getFreeClass($period, $course_subject, CareerSchoolYearPeer::retrieveByPk($career_school_year_id), $division); ?>
                  <?php endif ?>
                <?php endif ?>


                <?php $student_attendance = StudentAttendancePeer::retrieveByDateAndStudent(date('Y-m-d', $day), $student, $course_subject_id, $career_school_year_id) ?>

                <?php if ($student_attendance): ?>

                  <?php $total = $total + $student_attendance->getValue(); ?>
                  <td  class="<?php echo ($student_attendance->getStudentAttendanceJustification()) ? 'box_justificated' : $clasz ?>"  style="text-align:center">
                    <?php echo SchoolBehaviourFactory::getInstance()->getFormattedAssistanceValue($student_attendance); ?>
                  </td>

                <?php else: ?>
                  <td class="no_available"></td>
                <?php endif; ?>
              <?php endforeach; ?>
              <td style="text-align:center"><?php echo round($total, 2) ?></td>
              <td style="text-align:center"><?php echo $student->getTotalAbsences($career_school_year_id, null, $course_subject_id, false) ?></td>
              <td style="text-align:center"><?php echo round($student->getTotalAbsences($career_school_year_id, null, $course_subject_id), 2) ?></td>
                <?php endforeach; ?>
          </tr>
          <tr>
            <th><?php echo __('Students'); ?></th>
            <?php foreach ($days as $day): ?>
              <th class="attendance_day"><?php echo date('d/m', $day); ?></th>
            <?php endforeach; ?>
            <th class="attendance_day"><?php echo __('Total'); ?></th>
             <th class="attendance_day"><?php echo __('Total absences till today (without justification)'); ?></th>
            <th class="attendance_day"><?php echo __('Total absences till today'); ?></th>
          </tr>
        </tbody>
      </table>
    </div>
    <ul class="sf_admin_actions">
      <li><a href="javascript:history.go(-1)" class="sf_admin_action_go_back"><?php echo __('Back') ?></a></li>
    </ul>

  </div>
</div>

<?php if (count($days) > 31): ?>
  <script type="text/javascript">

    jQuery(document).ready(function(){

      var oTable = jQuery('#miTabla').dataTable( {
        "sScrollX": "100%",

        "bPaginate": false,
        "bLengthChange": false,
        "bFilter": false,
        "bSort": false,
        "bInfo": false,
        "bAutoWidth": false,

        "aoColumnDefs": [ { "sWidth": "15px", "aTargets": [ '_all' ] }]

      } );

      new FixedColumns( oTable, {
        "sLeftWidth": 'relative',
        "iLeftWidth": 16
      } );
    });

  </script>

  <?php endif;?>