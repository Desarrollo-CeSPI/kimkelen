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
<div class="report-title"><?php echo __('Admonition details'); ?></div>
<div style="clear:both"></div>
<?php $periods_array = CareerSchoolYearPeriodPeer::getPeriodsArrayForCourseType($division->getCourseType(), $division->getCareerSchoolYearId()); ?>


<div class="admonition_details">
  <?php foreach ($periods_array as $short_name => $period): ?>
    <?php if ($period->getIsClosed()): ?>
      <table class="gridtable">
        <thead>
          <tr>
            <td colspan="4" class="partial_average"><?php echo $period->getName() ?></td>
          </tr>
        </thead>
        <tbody>
          <?php if (StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctionsForPeriod($student, $division->getSchoolYear(), $period)): ?>
            <tr>
              <th><?php echo __('Resolution date') ?></th>
              <th><?php echo __('Reason') ?></th>
              <th><?php echo __('Disciplinary sanction type') ?></th>
              <th><?php echo __('Total') ?></th>
            </tr>
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
              <td colspan ="4" class="total">Total: <?php echo StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctionsForPeriod($student, $division->getSchoolYear(), $period) ?></td>
            </tr>
          </tfoot>
        </table>
      <?php else: ?>
        <tr>
          <td style="text-align:left"><?php echo __("Student doesn't have any disciplinary sanctions.") ?></td>
        </tr></tbody></table>
    <?php endif; ?>
  <?php endif; ?>
<?php endforeach; ?>
</div>

<div class="colsright">
  <div class="rowfirm_responsible">
    <div class="titletable"><?php echo __('Responsible signature') ?></div>
  </div>
  <div class="rowfirm_authority">
    <div class="titletable"><?php echo __('Authority signature') ?></div>
  </div>
</div>