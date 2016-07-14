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
 */ 
?>

<tbody class="print_body">
  <?php $i = 0; ?>
  <?php foreach ($course_subject->getCourseSubjectStudents() as $course_subject_student): ?>
    <?php if ($course_subject_student->getStudent()->getIsRegistered($course_subject->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getSchoolYear())): ?>
      <?php $i++ ?>
      <?php $course_result = $course_subject_student->getCourseResult(); ?>
      <tr>
        <td><?php echo $i ?></td>
        <td style="text-align: left; width: 30%"><?php echo $course_subject_student->getStudent() . ' (' . implode(' ,',$course_subject_student->getStudent()->getCurrentDivisions(($course->getCareerSchoolYear())? $course->getCareerSchoolYear()->getId(): null)). ')' ?></td>
        <?php foreach ($course_subject_student->getCourseSubjectStudentMarks() as $key => $cssm): ?>
              <td><?php echo ((!$cssm->getMark())? '' : $cssm->getMarkByConfig($configuration)); ?></td>
        <?php endforeach; ?>
        <td align="center"><?php echo ($final_period) ? $course_subject_student->getAverageByConfig($configuration) : '' ?></td>
        <td></td>
      </tr>
    <?php endif; ?>
  <?php endforeach ?>
</tbody>