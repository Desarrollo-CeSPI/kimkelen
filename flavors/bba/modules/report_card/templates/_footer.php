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
<?php $examination_repproveds = $student->getStudentRepprovedCourseSubjectForReportCards(SchoolYearPeer::retrieveLastYearSchoolYear($division->getCareerSchoolYear()->getSchoolYear())) ?>
<?php $has_to_show_repproveds = SchoolBehaviourFactory::getInstance()->showReportCardRepproveds() && !empty($examination_repproveds) && $student->checkIfRepprovedAreNotApproved($examination_repproveds) ?>
<div class="colsleft">
  <?php if ($division->hasCourseType(CourseType::TRIMESTER)): ?>
    <?php $periods = CareerSchoolYearPeriodPeer::getTrimesterPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>
    <?php if ($division->hasAttendanceForDay()): ?>
      <?php include_partial('trimester_attendance_for_day', array('student' => $student, 'periods' => $periods, 'division' => $division)); ?>
    <?php endif; ?>
    <div class="rowamon">
      <div class="titletable"><?php echo __('Admonitions') ?></div>
      <table class="lefttable">
        <tr>
          <th><?php echo __('1°T') ?></th>
          <th><?php echo __('2°T') ?></th>
          <th><?php echo __('3°T') ?></th>
          <th><?php echo __('Total') ?></th>
        </tr>
        <tr>
          <?php foreach ($periods as $period): ?>
            <td><?php echo StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctionsForPeriod($student, $division->getSchoolYear(), $period) ?></td>
          <?php endforeach; ?>
          <td><?php echo StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctions($student, $division->getSchoolYear(), $periods) ?></td>
        </tr>
      </table>
    </div>

  <?php elseif ($division->hasCourseType(CourseType::QUATERLY)): ?>
    <?php $periods = CareerSchoolYearPeriodPeer::getQuaterlyPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>
    <?php if ($division->hasAttendanceForDay()): ?>
      <?php include_partial('quaterly_attendance_for_day', array('student' => $student, 'periods' => $periods)); ?>
    <?php endif; ?>

  <?php elseif ($division->hasCourseType(CourseType::BIMESTER)): ?>
    <?php $periods = CareerSchoolYearPeriodPeer::getBimesterPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>
    <?php if ($division->hasAttendanceForDay()): ?>
      <?php include_partial('bimester_attendance_for_day', array('student' => $student, 'periods' => $periods)); ?>
    <?php endif; ?>

    <div class="rowins">
      <div class="titletable"><?php echo __('Behaviour') ?></div>
      <table class="lefttable">
        <tr>
        <tr>
          <th colspan="2" style="text-align: center"><?php echo __('1°C') ?></th>
          <th colspan="2" style="text-align: center"><?php echo __('2°C') ?></th>
          <th><?php echo __('Admonitions') ?></th>
        </tr>
        <tr>
          <th><?php echo __('1°B') ?></th>
          <th><?php echo __('2°B') ?></th>
          <th><?php echo __('1°B') ?></th>
          <th><?php echo __('2°B') ?></th>
          <td><?php echo StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctions($student, $division->getSchoolYear(), $periods) ?></td>
        </tr>
        <tr>
          <?php foreach ($periods as $period): ?>
            <td><?php echo ($student->getConductPeriod($period)) ? $student->getConductPeriod($period)->getConduct()->getShortName() : '-'; ?></td>
          <?php endforeach; ?>
        </tr>
      </table>
    </div>


  <?php endif; ?>

  <?php if ($has_to_show_repproveds): ?>
    <?php include_partial('career_subject_repproveds', array('student' => $student, 'division' => $division, 'examination_repproveds' => $examination_repproveds, 'has_to_show_attendances_per_day' => $division->hasAttendanceForDay())); ?>
  <?php endif ?>

</div>


<?php include_partial('signature_boxes') ?>