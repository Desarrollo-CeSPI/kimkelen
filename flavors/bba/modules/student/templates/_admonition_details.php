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

<span class="report-date" > <?php echo __('Issue date') ?>: <?php echo date('d/m/Y') ?> </span>
<div class="report-header">
  <div class="logo"><?php echo image_tag("kimkelen_logo.png", array('absolute' => true)) ?></div>
  <div class="header_row">
    <h2><?php echo __('Admonition details'); ?></h2>
    <div class="title"><?php echo __('Student') ?>: </div>
    <div class="name"><?php echo $student ?></div>
    <div class="header_right">
      <div class="title"><?php echo __('Course') ?>: </div>
      <div class="course"><?php echo $student_career_school_year->getYear() ?></div>
      <div class="title"><?php echo __('Division') ?>: </div>
      <div class="division"><?php echo $division->getDivisionTitle(); ?></div>
    </div>
  </div>
</div>

<div style="clear:both"></div>
<?php $periods_array = CareerSchoolYearPeriodPeer::getPeriodsArrayForCourseType($division->getCourseType(), $division->getCareerSchoolYearId()); ?>

<div>
  <div class="report-title"><?php echo __('Admonition details'); ?></div>
  <div class="admonition_details">


    <table class="gridtable">

      <thead>

        <?php if (count($student->getStudentDisciplinarySanctionsForSchoolYear($division->getSchoolYear())) != 0): ?>
          <tr>
            <th><?php echo __('Resolution date') ?></th>
            <th><?php echo __('Reason') ?></th>
            <th><?php echo __('Disciplinary sanction type') ?></th>
            <th><?php echo __('Total') ?></th>
            <th><?php echo __('Applicant') ?></th>
            <th><?php echo __('Observation') ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($student->getStudentDisciplinarySanctionsForSchoolYear($division->getSchoolYear()) as $student_disciplinary_sanction): ?>
            <tr>
              <td><?php echo $student_disciplinary_sanction->getFormattedRequestDate(); ?></td>
              <td><?php echo $student_disciplinary_sanction->getDisciplinarySanctionType(); ?></td>
              <td><?php echo $student_disciplinary_sanction->getSanctionType(); ?></td>
              <td><?php echo $student_disciplinary_sanction->getValue(); ?></td>
              <td><?php echo $student_disciplinary_sanction->getApplicant(); ?></td>
              <td><?php echo $student_disciplinary_sanction->getObservation(); ?></td>
            </tr>
          <?php endforeach; ?>

        <?php else: ?>
          <tr>
            <td style="text-align:left"><?php echo __("Student doesn't have any disciplinary sanctions.") ?></td>
          </tr>
        <?php endif; ?>

      </tbody>
      <tfoot>
        <tr>
          <td colspan ="6" class="report-total">Total anual: <span class="big-total"><?php echo $student->countStudentDisciplinarySanctionsForSchoolYear($division->getSchoolYear()) ?></span></td>
        </tr>
        <tr>
          <td colspan ="6" class="report-total">Total en <?php echo $student->getCareerSchoolYearsNames() ?>: <span class="big-total"><?php echo StudentDisciplinarySanctionPeer::countTotalValueForStudent($student) ?></span></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<div style="clear:both"></div>
<br>
<div class="colsright">
  <div class="rowfirm_responsible">
    <div class="titletable"><?php echo __('Responsible signature') ?></div>
  </div>

  <div class="rowfirm_authority">
    <div class="titletable"><?php echo __('Authority signature') ?></div>
  </div>
</div>
<div style="clear:both;"></div>
