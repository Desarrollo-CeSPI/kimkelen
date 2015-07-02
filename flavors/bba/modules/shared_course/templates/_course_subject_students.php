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
<h3><strong><?php echo __('Subject %subject%', array('%subject%' => $course_subject->getCareerSubject()->getSubject()))?></strong></h3>

<?php $career = $course_subject->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getCareer(); ?>
<?php $final_period = $course_subject->isFinalPeriod(); ?>

<table>
  <thead>
    <tr>
      <th>
        <?php echo __('File number'); ?>
      </th>
      <th>
        <?php echo __('Student'); ?>
      </th>
      <?php $subject_configuration = $course_subject->getCareerSubjectSchoolYear()->getConfiguration(); ?>
      
      <?php for ($i = 1; $i <= $subject_configuration->getCourseMarks(); $i++): ?>
        <th>
          <?php if(($subject_configuration->getEvaluationMethod() == EvaluationMethod::FINAL_PROM) && ($i == $subject_configuration->getCourseMarks())): ?>
            <?php echo __('Final mark'); ?>
          <?php else: ?>
            <?php echo __('Mark %number%', array('%number%' => $i));?>
          <?php endif; ?>
        </th>
      <?php endfor; ?>
      <?php if ($final_period ): ?>
        <?php if($subject_configuration->getEvaluationMethod() == EvaluationMethod::FINAL_PROM): ?>
          <th><?php echo __('Final average') ?></th>
        <?php else: ?>
          <th><?php echo __('Average') ?></th>
        <?php endif; ?>
        <th><?php echo __('Result'); ?></th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($course_subject->getCourseSubjectStudents() as $course_subject_student): ?>
      <?php $course_result = $course_subject_student->getCourseResult(); ?>

      <tr<?php echo (($final_period && !is_null($course_result)) ? " class='".$course_result->getClass()."'" : ""); ?>>

        <td><?php echo $course_subject_student->getStudent()->getFileNumber($career); ?></td>
        <td><?php echo $course_subject_student->getStudent(); ?></td>
        <?php $tmp_sum = 0; ?>

        <?php foreach ($course_subject_student->getCourseSubjectStudentMarks() as $cssm): ?>
          <td><?php echo $cssm->getStringMark(); ?></td>
          <?php $tmp_sum += $cssm->getStringMark();?>
        <?php endforeach; ?>
        <?php if ($final_period ): ?>
          <td><?php echo $course_subject_student->getMarksAverage(); ?></td>
          <td><?php echo $course_result; ?></td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>