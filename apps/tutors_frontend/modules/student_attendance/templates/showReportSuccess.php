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

<div class="row">
  <div class="col-md-12">
    <?php include_partial('mainFrontend/personal_info', array('person' => $student)) ?>

    <div class="col-md-8">
      <div class="row title-box">
        <div class="col-md-12 title-icon">
          <?php echo image_tag('frontend/attendance.svg', array('alt' => __('Attendance details'))); ?>
          <span class="title-text"> <?php echo __('Attendance details');?> - <?php echo $school_year->getYear()?> </span>
        </div>
      </div>

      <div class="row action-box">
        <div class="col-md-12 text-right">
          <?php echo link_to(
            '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>' . __('Go back') .'',
            $go_back,
            array('class' => 'btn btn btn-primary')
          )?>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="data-box">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th><?php echo __('Day') ?></th>
                  <th><?php echo __('Absence') ?></th>
                  <?php if ($student->hasAttendancesPerSubject()): ?>
                  <th><?php echo __('Subject') ?></th>
                  <?php endif; ?>
                  <th><?php echo __('Is justified') ?></th>
                  <th><?php echo __('Justification type') ?></th>
                  <th><?php echo __('Description') ?></th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($student_career_school_years as $student_career_school_year): ?>
                <?php foreach ($student_career_school_year->getDivisions() as $division): ?>
                  <?php foreach ($student->getAbsencesReport($student_career_school_year->getCareerSchoolYearId()) as $absence): ?>
                    <tr>
                      <td><?php echo $absence->getFormattedDay(); ?></td>
                      <td><?php echo $absence->getValueString() ?></td>
                      <?php if ($student->hasAttendancesPerSubject()): ?>
                        <td><?php echo ($course_subject = $absence->getCourseSubject()) ? $absence->getCourseSubject() : '-' ?></td>
                      <?php endif; ?>
                      <td><?php echo ($justification = $absence->getStudentAttendanceJustification()) ? 'Sí' : 'No' ?></td>
                      <td><?php echo ($type = $absence->getStudentAttendanceJustification()) ? $absence->getStudentAttendanceJustification()->getJustificationType() : '-' ?></td>
                      <td><?php echo ($justification = $absence->getStudentAttendanceJustification()) ? $absence->getStudentAttendanceJustification()->getObservation() : '-' ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endforeach; ?>
              <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="5" class="text-right"><?php echo __('Total') . ': ' . round($student->getTotalAbsencesReport($division->getCareerSchoolYearId(), false), 2) ?></td>
                </tr>
                <tr>
                  <td colspan="5" class="text-right"><?php echo __('Unjustified') . ': ' . round($student->getTotalAbsencesReport($division->getCareerSchoolYearId()), 2) ?></td>
                </tr>
                <tr>
                  <td colspan="5" class="text-right"><?php echo __('Justified') . ': ' . round($student->getTotalJustificatedAbsencesReport($division->getCareerSchoolYearId()), 2) ?></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
