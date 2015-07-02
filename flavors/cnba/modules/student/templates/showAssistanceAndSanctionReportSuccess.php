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
<?php use_helper('Javascript', 'Object', 'I18N', 'Asset') ?>
<?php use_stylesheet('/css/print-assistanceAndSanction.css', '', array('media' => 'print')) ?>
<?php use_stylesheet('/css/assistanceAndSanction.css') ?>

<div class="non-printable">
  <a href="#" onclick="window.print(); return false;"><?php echo __('Print') ?></a>
  <a href="<?php echo url_for('student') ?>"><?php echo __('Go back') ?></a>
</div>
<?php foreach ($student_career_school_years as $student_career_school_year): ?>
  <?php $school_year = $student_career_school_year->getSchoolYear(); ?>
  <?php foreach ($student_career_school_year->getDivisions() as $division): ?>
    <div class="report-wrapper">
      <div class="report-header">
        <div class="logo"><?php echo image_tag("kimkelen_logo.png", array('absolute' => true)) ?></div>

        <div class="header_row">
          <h2><?php echo __('Reporte de inasistencias y sanciones'); ?></h2>
          <div class="title"><?php echo __('Student') ?>: </div>
          <div class="name"><?php echo $student ?></div>
          <div class="header_right">
            <div class="title"><?php echo __('Course') ?>: </div>
            <div class="course"><?php echo $student_career_school_year->getYear() ?></div>
            <div class="title"><?php echo __('Division') ?>: </div>
            <div class="division"><?php echo $division->getDivisionTitle(); ?></div>
            <div class="school_year"><?php echo __('School year') . " " . $school_year ?></div>
          </div>
        </div>
      </div>
      <div style="clear:both"></div>
      <div class="report-title"><?php echo __('Absences') ?></div>
      <div style="clear:both"></div>
      <?php if (count($student->getAbsences($student_career_school_year->getCareerSchoolYearId())) == 0): ?>
        <div style="clear:both"></div>
        <span class="report-notice"><?php echo __('No se registraron inasistencias para este alumno.'); ?></span>
      <?php else: ?>
        <table class="print_table">
          <thead>
            <tr>
              <th><?php echo __('Day') ?></th>
              <th><?php echo __('Absence') ?></th>
              <th><?php echo __('Description') ?></th>
              <?php if ($student->hasAttendancesPerSubject()): ?>
                <th><?php echo __('Subject') ?></th>
              <?php endif; ?>
              <th><?php echo __('Is justified') ?></th>
              <th><?php echo __('Justification type id') ?></th>
            </tr>
          </thead>
          <tbody class="print_body">
            <?php foreach ($student->getAbsences($student_career_school_year->getCareerSchoolYearId()) as $absence): ?>
              <tr>
                <td><?php echo $absence->getFormattedDay(); ?></td>
                <td><?php echo $absence->getValueString() ?></td>
                <td><?php echo $absence->getAbsenceType()->getDescription() ?></td>
                <?php if ($student->hasAttendancesPerSubject()): ?>
                  <td><?php echo ($course_subject = $absence->getCourseSubject()) ? $absence->getCourseSubject() : '-' ?></td>
                <?php endif; ?>
                <td><?php echo ($justification = $absence->getStudentAttendanceJustification()) ? 'Sí' : 'No' ?></td>
                <td><?php echo ($type = $absence->getStudentAttendanceJustification()) ? $absence->getStudentAttendanceJustification()->getJustificationType() : '-' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="6" class="report-total">
                <?php
                echo __('Total') . ': '
                . round($student->getTotalAbsences($division->getCareerSchoolYearId(), null, null, false), 2)
                ?>
              </td>
            </tr>
            <tr>
              <td colspan="6" class="report-total">
                <?php
                echo __('Total unjustified') . ': '
                . round($student->getTotalAbsences($division->getCareerSchoolYearId(), null), 2)
                ?>
              </td>
            </tr>
            <tr>
              <td colspan="6" class="report-total">
                <?php
                echo __('Total justified') . ': '
                . round($student->getTotalJustificatedAbsences($division->getCareerSchoolYearId(), null, null), 2)
                ?>
              </td>
            </tr>
          </tfoot>
        </table>

      <?php endif; ?>
      <br><br>
      <div style="clear:both"></div>

      <div class="report-title" style="background-color:#f2f2f2;"><?php echo __('Admonition details'); ?></div>

      <?php if ($student->countStudentDisciplinarySanctionsForSchoolYear($school_year) == 0): ?>
        <span class="report-notice"><?php echo __('No se registraron sanciones para este alumno.'); ?></span>
      <?php else: ?>
        <?php $periods_array = CareerSchoolYearPeriodPeer::getPeriodsArrayForCourseType($division->getCourseType(), $division->getCareerSchoolYearId()); ?>

        <div>
          <div class="admonition_details">
            <?php foreach ($periods_array as $short_name => $period): ?>
              <?php if (StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctionsForPeriod($student, $division->getSchoolYear(), $period)): ?>
                <table class="print_table">
                  <thead>
                    <tr>
                      <th><?php echo __('Resolution date') ?></th>
                      <th><?php echo __('Motivo') ?></th>
                      <th><?php echo __('Disciplinary sanction type') ?></th>
                      <th><?php echo __('Total') ?></th>
                    </tr>
                    </head>
                  <tbody class="print_body">
                    <?php foreach (StudentDisciplinarySanctionPeer::retrieveStudentDisciplinarySanctionsForPeriod($student, $division->getSchoolYear(), $period) as $student_disciplinary_sanction): ?>

                      <tr>
                        <td><?php echo $student_disciplinary_sanction->getFormattedRequestDate(); ?></td>
                        <td><?php echo $student_disciplinary_sanction->getDisciplinarySanctionType(); ?></td>
                        <td><?php echo $student_disciplinary_sanction->getSanctionType(); ?></td>
                        <td><?php echo $student_disciplinary_sanction->getValue(); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan ="4" class="report-total">Total: <?php echo StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctionsForPeriod($student, $division->getSchoolYear(), $period) ?></td>
                    </tr>
                  </tfoot>
                </table>
              <?php endif; ?>

            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

    <?php endforeach ?>
  <?php endforeach ?>

  <div class="colsright">
    <div class="rowfirm_authority">
      <div class="titletable"><?php echo __('Authority signature') ?></div>
    </div>
    <div class="rowfirm_responsible">
      <div class="titletable"><?php echo __('Responsible signature') ?></div>
    </div>
  </div>
</div>