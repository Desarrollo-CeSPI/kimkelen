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

<?php $career = $course_subject->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getCareer(); ?>
<?php $final_period = $course_subject->isFinalPeriod(); ?>
<?php $course_subject_configurations = $course_subject->getCourseSubjectConfigurations() ?>
<?php $configuration = $course_subject->getCareerSubjectSchoolYear()->getConfiguration() ?>
<?php $marks = $configuration->getCourseMarks() ?>

<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_marks_count">
  <div>
    <label for="marks_count"> <?php echo __('Marks count'); ?> </label>
    <?php echo $configuration->getCourseMarks(); ?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_marks_course_minimun_mark">
  <div>
    <label for="course_minimun_mark"> <?php echo __('Course minimun mark'); ?> </label>
    <?php echo $configuration->getCourseMinimunMark(); ?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<?php if ($course_subject->hasAttendanceForSubject()): ?>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_marks_assistance">
    <div>
      <label for="attendance_for_subject"> <?php echo __('Attendance for subject'); ?> </label>
      <?php foreach ($course_subject_configurations as $config): ?>
        <div>
          <?php echo __('Maximun absences for') ?>
          <?php echo $course_subject->getConfigurationForPeriod($config->getCareerSchoolYearPeriod())->getCareerSchoolYearPeriod() . ': ' . $course_subject->getMaxAbsenceForPeriod($config->getCareerSchoolYearPeriod()); ?>
        </div>
      <?php endforeach; ?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
<?php endif ?>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_students">
  <div>
    <label for="students"> <?php echo __('Students'); ?> </label>
    <table>
      <thead>
        <tr>
          <th><?php echo __('File number'); ?></th>
          <th><?php echo __('Student'); ?></th>
          <?php if ($course_subject->hasAttendanceForSubject()): ?>
            <th><?php echo __('Absences'); ?></th>
          <?php endif ?>
          <?php for ($i = 1; $i <= $marks; $i++): ?>
            <th><?php echo __(SchoolBehaviourFactory::getInstance()->getMarkTitle($i), array('%number%' => $i)) ?></th>
          <?php endfor ?>
          <?php if ($final_period): ?>
            <th><?php echo __('Average'); ?></th>
            <th><?php echo __('Result'); ?></th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($course_subject->getCourseSubjectStudents() as $course_subject_student): ?>
          <?php $course_result = $course_subject_student->getCourseResult(); ?>
          <tr<?php echo (($final_period && !is_null($course_result)) ? " class='" . $course_result->getClass() . "'" : ""); ?>>

            <td><?php echo $course_subject_student->getStudent()->getFileNumber($career) ?></td>
            <td><?php echo $course_subject_student->getStudent() ?></td>
            <?php if ($course_subject->hasAttendanceForSubject()): ?>
              <td><?php echo round($course_subject_student->getTotalAbsences(), 2); ?></td>
            <?php endif ?>

            <?php foreach ($course_subject_student->getCourseSubjectStudentMarks() as $cssm): ?>
              <td><?php echo (!$cssm->getMark()? '' : $cssm->getMarkByConfig($configuration)); ?></td>
            <?php endforeach; ?>
            <?php if ($final_period): ?>
              <?php if ($course_subject_student->getIsNotAverageable()): ?>
                <td></td>
                <td><?php echo SchoolBehaviourFactory::getEvaluatorInstance()->getExemptString() ?></td>
              <?php else: ?>
                <td><?php echo $course_subject_student->getAverageByConfig($configuration) ?></td>
                <td><?php echo $course_result ?></td>
              <?php endif; ?>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>