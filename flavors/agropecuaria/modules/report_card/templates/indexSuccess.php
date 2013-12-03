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
<?php use_stylesheet('/css/report-card.css') ?>
<?php use_stylesheet('/css/print-report-card.css', '', array('media' => 'print')) ?>
<?php use_helper('Asset', 'I18N') ?>

<div class="non-printable">
  <div><a href="<?php echo url_for('@export_report_cards?sf_format=pdf') ?>"><?php echo __('Export') ?></a></div>
  <div><a href="<?php echo url_for($back_url) ?>"><?php echo __('Go back') ?></a></div>
</div>

<?php foreach ($students as $student): ?>
  <?php $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $division->getCareerSchoolYear()) ?>
  <div class="report-wrapper">
    <?php include_partial('header', array('student' => $student, 'division' => $division, 'career_id' => $career_id, 'school_year' => $student_career_school_year->getSchoolYear(), 'student_career' => CareerStudentPeer::retrieveByCareerAndStudent($career_id, $student->getId()))); ?>
    <div class="report-content">

      <?php $periods = CareerSchoolYearPeriodPeer::getQuaterlyPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>

      <?php $school_year = $division->getSchoolYear(); ?>
      <?php $course_subject_students = $student->getCourseSubjectStudentsForSchoolYear($school_year); ?>

      <?php include_partial('course_subject_quaterly', array('student' => $student, 'course_subject_students' => $course_subject_students, 'periods' => $periods, 'has_attendance_for_subject' => false, 'student_career_school_year' => $student_career_school_year)) ?>
      <?php $examination_repproveds = $student->getStudentRepprovedCourseSubjectForSchoolYear(SchoolYearPeer::retrieveLastYearSchoolYear($division->getCareerSchoolYear()->getSchoolYear())) ?>
      <?php $has_to_show_repproveds = SchoolBehaviourFactory::getInstance()->showReportCardRepproveds() && !empty($examination_repproveds) ?>

      <div class="footer" style="width: 100%">
        <?php include_partial('footer', array('student' => $student, 'division' => $division)); ?>
      </div>
    </div>

    <div class="report-content">
      <?php if ($has_to_show_repproveds): ?>
        <hr class="hr_break">
        <?php include_partial('career_subject_repproved_details', array('examination_repproveds' => $examination_repproveds)); ?>
      <?php endif; ?>
      <div style="clear:both"></div>
      <hr class="hr_break">
      <div class="title"><?php echo __('Admonition details'); ?></div>

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
      <?php include_partial('signature_boxes'); ?>
    </div>
  </div>
  <div style="clear:both"></div>
  <div style="page-break-before: always;"></div>
<?php endforeach; ?>
