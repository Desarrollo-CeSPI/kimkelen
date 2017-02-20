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
 */
?>

<div class="row">
  <div class="col-md-12">
    <?php include_partial('mainFrontend/personal_info', array('person' => $student)) ?>

    <div class="col-md-8">
      <div class="row title-box">
        <div class="col-md-12 title-icon">
          <?php echo image_tag("frontend/attendance.svg", array('alt' => __('Attendance'))); ?>
          <span class="title-text"> <?php echo __("Attendance");?> - <?php echo $school_year->getYear()?> </span>
        </div>
      </div>

      <div class="row action-box">
        <div class="col-md-12 text-right">
          <?php echo link_to(
            '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>' . __('Go back') .'',
            '@homepage',
            array('class' => 'btn btn btn-primary')
          )?>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="data-box">
          <?php if(is_null($student_career_school_year)):?>
            <div class="alert alert-danger"><?php echo __('No se registraron inasistencias para este alumno.'); ?></div>
          <?php else: ?>
            <?php if(!$student->hasAttendancesPerSubject()): ?>
              <?php include_partial('attendance_per_day', array('student_career_school_year'=>$student_career_school_year,'division'=>$division,'student'=>$student)); ?>
            <?php else: ?>
              <?php include_partial('attendance_per_subject', array('student_career_school_year'=>$student_career_school_year,'student' => $student)); ?>
            <?php endif; ?>
            <div class="text-center">
              <?php echo link_to(
                '<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> ' . __('Show report') . '',
                'student_attendance/showReport?student_id=' . $student->getId(),
                array('class' => 'btn btn-success')
              )?>
            </div>
          <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>