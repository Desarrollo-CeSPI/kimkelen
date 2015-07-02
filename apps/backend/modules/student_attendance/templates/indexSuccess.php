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
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<div id="sf_admin_container">
  <h1>
    <?php echo __('Load Attendances') ?>
  </h1>

  <div id="sf_admin_content">
    <form action="<?php echo url_for($route) ?>" method="post" >
      <ul class="sf_admin_actions">
        <li><input type="submit" value="<?php echo __('Save', array(), 'sf_admin') ?>" /></li>
      </ul>

      <?php if ($form->hasGlobalErrors()): ?>
        <?php echo $form->renderGlobalErrors() ?>
      <?php endif ?>

      <fieldset>
        <?php echo $form->renderHiddenFields() ?>
        <?php echo $form['day']->renderRow() ?>
        <?php echo $form['career_school_year_id']->renderRow() ?>
        <?php echo $form['year']->renderRow() ?>
        <?php if (!isset($show_courses)): ?>
          <?php echo $form['division_id']->renderRow() ?>
        <?php endif ?>

        <?php if (isset($show_courses)): ?>
          <?php echo $form['course_subject_id']->renderRow() ?>
        <?php else: ?>
          <?php $course_subject_id = null; ?>
        <?php endif ?>

        <?php if (count($students)): ?>
          <?php include_partial('student_attendance/information_box') ?>
          <table style="width: 100%">
            <thead>
              <tr>
                <th><?php echo __('Student') ?></th>
                <?php foreach ($days as $d): ?>
                  <th><?php echo date('d/m/Y', $d) ?></th>
                <?php endforeach ?>
                <th><?php echo $unformatted_date ?></th>
                <?php foreach ($career_school_year_periods as $period): ?>
                  <th><?php echo $period->getName() ?></th>
                <?php endforeach ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($students as $student): ?>
                <tr>
                  <td><?php echo $student ?></td>
                  <?php foreach ($days as $d): ?>

                    <?php $student_attendance = StudentAttendancePeer::retrieveByDateAndStudent(date('Y-m-d', $d), $student, $course_subject_id) ?>
                    <td class="<?php ($student_attendance) ? $student_attendance->getStudentAttendanceJustification() and print 'attendance_justificated'  : '' ?>">
                      <?php echo ($student_attendance) ? $student_attendance->getValueString() : 'n/a' ?>
                      <?php if ($student_attendance && $student_attendance->getStudentAttendanceJustification()): ?>
                        <?php echo link_to(image_tag('sanction.png'), 'attendance_justification/show?id=' . $student_attendance->getStudentAttendanceJustificationId()) ?>
                      <?php endif ?>
                    </td>
                  <?php endforeach ?>


                  <?php $student_attendance = StudentAttendancePeer::retrieveByDateAndStudent($day, $student, $course_subject_id) ?>
                  <td class="<?php ($student_attendance) ? $student_attendance->getStudentAttendanceJustification() and print 'attendance_justificated'  : '' ?>">
                    <?php echo $form['student_' . $student->getId()]->renderError() ?>
                    <?php echo $form['student_' . $student->getId()]->render() ?>
                    <?php echo $form['student_' . $student->getId() . '_attendance_id']->render() ?>
                    <?php if (isset($form['student_' . $student->getId() . '_attendance'])): ?>
                      <?php echo $form['student_' . $student->getId() . '_attendance']->renderError() ?>
                      <?php echo $form['student_' . $student->getId() . '_attendance']->render() ?>
                    <?php elseif (!is_null($student_attendance) && $student_attendance->getStudentAttendanceJustification()): ?>
                      <?php echo $student_attendance->getValueString() ?>
                      <?php echo link_to(image_tag('sanction.png'), 'attendance_justification/show?id=' . $student_attendance->getStudentAttendanceJustificationId()) ?>
                    <?php else: ?>
                      <?php echo __('Is free') ?>
                    <?php endif ?>
                  </td>
                  <?php foreach ($career_school_year_periods as $period): ?>
                    <?php $clazz = $student->getFreeClass($period, isset($course_subject_id) ? $course_subject_id : null) ?>
                    <td class="<?php echo $clazz ?>">
                      <div>
                        <?php echo __('Absences:  %total%', array(
                            '%total%' => round($student->getTotalAbsences($career_school_year_id, $period, $course_subject_id , true), 2)))
                        ?>
                      </div>
                      <div>
                        <?php echo __('Remaining: %total%', array(
                            '%total%' => round($student->getRemainingAbsenceFor($period, $course_subject_id , true), 2)))
                        ?>
                      </div>
                      <?php if ($clazz == 'free'): ?>
                        <div><?php echo __('Is free') ?></div>
                      <?php endif ?>
                    </td>
                  <?php endforeach ?>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>

        <?php else: ?>
          <div class="sf_admin_form_row">
            <?php echo __("There are no students for load attendances") ?>
          </div>
        <?php endif ?>
      </fieldset>


      <ul class="sf_admin_actions">
        <li><input type="submit" value="<?php echo __('Save', array(), 'sf_admin') ?>" /></li>
      </ul>

    </form>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>