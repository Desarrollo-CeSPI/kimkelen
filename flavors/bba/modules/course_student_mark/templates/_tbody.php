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
<tbody class="print_body">
  <?php $i = 0;?>
  <?php $array_marks = array(7,8,9,10);?>
  <?php foreach ($course_subject->getCourseSubjectStudentsForPrintReport() as $course_subject_student): ?>
    <?php $last_scsy = $course_subject_student->getStudent()->getLastStudentCareerSchoolYearCoursed(); ?>
    <?php $scsy = $course_subject_student->getStudent()->getLastStudentCareerSchoolYear(); ?>
    <?php if ((!is_null($scsy) && $scsy->getStatus() != StudentCareerSchoolYearStatus::WITHDRAWN) || (!is_null($last_scsy) && $last_scsy->getCareerSchoolYear()->getSchoolYear()->getYear() >= $course_subject->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getSchoolYear()->getYear())): ?>
      <?php $i++ ?>
      <?php $course_result = $course_subject_student->getCourseResult(); ?>
      <tr>
        <td><?php echo $i ?></td>
        <td style="text-align: left; width: 30%"><?php echo $course_subject_student->getStudent() . ' (' . implode(' ,',$course_subject_student->getStudent()->getCurrentDivisions(($course->getCareerSchoolYear())? $course->getCareerSchoolYear()->getId(): null)). ')' ?></td>
        <?php foreach ($course_subject_student->getCourseSubjectStudentMarks() as $key => $cssm): ?>
              <td><?php echo ((!$cssm->getMark())? '' : $cssm->getMarkByConfig($configuration)); ?></td>
        <?php endforeach; ?>
        <td align="center">
            <?php if($final_period): ?>
        <?php if($course_subject_student->getIsNotAverageable() && !is_null($course_subject_student->getNotAverageableCalification())) : ?>
    
            <?php if($course_subject_student->getNotAverageableCalification() == NotAverageableCalificationType::APPROVED): ?>
              <?php echo __("Trayectoria completa"); ?>
            <?php elseif(in_array($course_subject_student->getNotAverageableCalification(),$array_marks)): ?> 
                 <?php echo $course_subject_student->getNotAverageableCalification();?>
            <?php else:?> 
                <?php echo __("Trayectoria en curso"); ?>
            <?php endif; ?>
        <?php else : ?>
            <?php echo $course_subject_student->getAverageByConfig($configuration) ; ?>
        <?php endif; ?>
        
        <?php else : ?>
        <?php echo '' ?>
        <?php endif; ?>
            </td>
        <td></td>
      </tr>
    <?php endif; ?>
  <?php endforeach ?>
</tbody>

