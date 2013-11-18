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
<?php $marks = $course_subject_students['marks'];?>
<?php $periods = $course_subject_students['periods'];?>
<?php unset($course_subject_students['marks']);?>
<?php unset($course_subject_students['periods']);?>

<?php $evaluator_instance = SchoolBehaviourFactory::getEvaluatorInstance() ?>


<?php $columns = 6 + $marks + count($evaluator_instance->getExaminationNumbers())?>

<?php if (count($course_subject_students)): ?>
  <table id="marks-table">

      <tr>
        <th colspan="<?php echo $columns?>">
          <?php echo __(CourseType::getInstance('CourseType')->getStringFor($course_type))?>
        </th>
      <tr>

      <tr>
        <th id="subject"><?php echo __("Subject") ?></th>
        <?php for($i = 1; $i <= $marks; $i++):?>
          <th><?php echo __(SchoolBehaviourFactory::getInstance()->getMarkTitle($i, $course_type), array('%number%' => $i)) ?></th>
        <?php endfor?>
        <th><?php echo __("Course Average") ?></th>
        <th><?php echo __("Course Status") ?></th>

        <?php foreach($evaluator_instance->getExaminationNumbers() as $number => $name):?>
          <th><?php echo $name ?></th>
        <?php endforeach?>

        <th><?php echo __("Repproved examinations") ?></th>
        <th><?php echo __("Final Average") ?></th>
        <th><?php echo __("Actions") ?></th>
      </tr>
      <?php $x = 0; ?>
      <?php foreach ($course_subject_students as $course_subject_student): ?>
        <tr>
          <td class="subject"><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject() ?></td>

          <?php for ($i = 1 ; $i <= $marks ; $i++): ?>
            <td><?php echo ($mark = $course_subject_student->getMarkFor($i)) ? $mark : "-" ?></td>
          <?php endfor ?>

          <td><?php echo $course_subject_student->getMarksAverage() ?></td>

          <td><?php echo ($course_result = $course_subject_student->getCourseResult()) ? $course_result->getResultStr() : '' ?></td>

          <?php foreach($evaluator_instance->getExaminationNumbers() as $number => $name):?>
            <td>
              <?php echo ($course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber($number)) ? $course_subject_student_examination->getMarkStr() : '' ?>
              <br/>
              <?php echo is_null($course_subject_student_examination) ? '' : ' (' . $course_subject_student_examination->getDate('d/m/y') . ') '?>
            </td>
          <?php endforeach?>

          <td><?php echo $course_subject_student->getStudentRepprovedCourseSubjectStrings(); ?></td>

          <td><?php echo ($course_subject_student->getStudentApprovedCareerSubject()) ? $course_subject_student->getFinalMark() : "" ?></td>

          <td>
            <div>
              <?php echo link_to(__("Details"), "student/historyDetails?career_student_id=".$career_student->getId()."&course_subject_student_id=".$course_subject_student->getId() . '&back_url=' .$back_url ) ?>
            </div>
            <?php if ($sf_user->hasCredential('edit_closed_examination') && $sf_user->hasCredential('edit_examination_subject_califications')):?>
              </div>
                <?php echo link_to(__('Edit'), 'student/editCourseSubjectStudentHistory?course_subject_student_id=' . $course_subject_student->getId());?>
              </div>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach ?>
    </table>
  <?php endif ?>