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
<?php $marks = $course_subject_students['marks']; ?>
<?php $periods = $course_subject_students['periods']; ?>
<?php unset($course_subject_students['marks']); ?>
<?php unset($course_subject_students['periods']); ?>

<?php

die(var_dump(SchoolBehaviourFactory::getEvaluatorInstance()));
$evaluator_instance = SchoolBehaviourFactory::getEvaluatorInstance();



?>


<?php $columns = 6 + $marks + count($evaluator_instance->getExaminationNumbers()) ?>

<?php if (count($course_subject_students)): ?>
  <div class="table-responsive">
  <table class="table table-condensed">
    <tr>
      <th colspan="<?php echo $columns ?>"  class="success">
        <?php echo __(CourseType::getInstance('CourseType')->getStringFor($course_type)) ?>
      </th>
    <tr>

    <tr class="success">
      <th><?php echo __("Subject") ?></th>
      <?php for ($i = 1; $i <= $marks; $i++): ?>
        <th><?php echo __(SchoolBehaviourFactory::getInstance()->getMarkTitle($i, $course_type), array('%number%' => $i)) ?></th>
      <?php endfor ?>
      <th><?php echo __("Course Average") ?></th>
      <th><?php echo __("Course Status") ?></th>

      <?php foreach ($evaluator_instance->getExaminationNumbers() as $number => $name): ?>
        <th><?php echo $name ?></th>
      <?php endforeach ?>

      <th><?php echo __("Repproved examinations") ?></th>
      <th><?php echo __("Final Average") ?></th>
    </tr>
    <?php $x = 0; ?>
    <?php foreach ($course_subject_students as $course_subject_student): ?>
      <tr>
        <td><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject() ?></td>

        <?php for ($i = 1; $i <= $marks; $i++): ?>
          <?php if ($course_subject_student->getIsNotAverageable()): ?>
            <td><?php echo SchoolBehaviourFactory::getEvaluatorInstance()->getExemptString() ?></td>

          <?php else: ?>
            <td><?php echo ($mark = $course_subject_student->getMarkFor($i)) ? $mark->getMarkByConfig($course_subject_student->getConfiguration()) : "-" ?></td>
          <?php endif; ?>
        <?php endfor ?>

        <?php if ($course_subject_student->getIsNotAverageable()): ?>
          <td></td>

        <?php else: ?>
          <td><?php echo $course_subject_student->getAverageByConfig($course_subject_student->getConfiguration()) ?></td>
        <?php endif; ?>

        <td><?php echo ($course_result = $course_subject_student->getCourseResult()) ? $course_result->getResultStr() : '' ?></td>

        <?php foreach ($evaluator_instance->getExaminationNumbers() as $number => $name): ?>
          <td>
            <?php echo ($course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber($number)) ? $course_subject_student_examination->getMarkStrByConfig($course_subject_student->getConfiguration()) : '' ?>
            <br/>
            <?php echo is_null($course_subject_student_examination) ? '' : $course_subject_student_examination->getFormattedDate() ?>
          </td>
        <?php endforeach ?>

        <td><?php echo $course_subject_student->getStudentRepprovedCourseSubjectStrings(); ?></td>

	      <?php $student = $course_subject_student->getStudent(); ?>
        <td><?php echo ($course_subject_student->getStudentApprovedCareerSubject()) ? $student->getPromDef($course_subject_student->getCourseResult()) : "" ?></td>
      </tr>
    <?php endforeach ?>
  </table>
  </div>

  <?php endif ?>
