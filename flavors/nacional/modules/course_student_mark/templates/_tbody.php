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
<?php $is_block = $course->getYear() == 1 || $course->getYear() == 2?>
<?php $i = 0;?>
<?php foreach ($course_subject->getCourseSubjectStudents() as $course_subject_student): ?>
  <?php $i++?>
  <?php $course_result = $course_subject_student->getCourseResult(); ?>
  <tr>
    <td><?php echo $i?></td>
    <td style="text-align: left"><?php echo $course_subject_student->getStudent() . ' (' . implode(' ,',$course_subject_student->getStudent()->getCurrentDivisions(($course->getCareerSchoolYear())? $course->getCareerSchoolYear()->getId(): null)). ')' ?></td>
    <?php foreach ($course_subject_student->getCourseSubjectStudentMarks() as $cssm): ?>
      <td><?php echo ((!$cssm->getMark())?__('free'): $cssm->getMark()?  $cssm : ''); ?></td>
    <?php endforeach; ?>
    
    <td><?php echo ($final_period)? $course_subject_student->getMarksAverage() : '' ?></td>
	  <?php $c = new num2text(); ?>
	  <td><?php echo ($final_period)? $c->num2str($course_subject_student->getMarksAverage()) : '' ?></td>
    <td></td>
    <td></td>
    <?php if ($is_block):?>
      <td></td>
      <td></td>
      <td></td>
    <?php endif?>
    <td></td>
    <td></td>
    <td></td>
  </tr>
<?php endforeach ?>
