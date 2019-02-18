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
<table class="print_table" cellspacing="0" id="export_to_excel" >
    <thead>
      <tr class="printColumns">
        <th rowspan="2"><?php echo __('Students'); ?></th>
        <?php foreach ($course_subjects as $i => $course) : ?>
          <th colspan="<?php echo count(SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationNumbers()) + 1 ?>">
              <?php echo $course->getCareerSubjectSchoolYear()->getCareerSubject()->getSubject()->getFantasyName(); ?></th>
        <?php endforeach; ?>
          <th>Previas</th>
      </tr>
      <tr>
        <?php foreach ($course_subjects as $i => $course) : ?>
          <th class="mark">Prom</th>
            <?php  $examinations = SchoolBehaviourFactory::getEvaluatorInstance()->getExaminationNumbers()?>
          <?php foreach ($examinations as $e): ?>
          <th class="mark"><?php echo $e; ?></th>
          <?php endforeach; ?>
        <?php endforeach; ?>
        <th class="mark"></th>
      </tr>
    </thead>
    <tbody class="print_body">
      <?php foreach ($students as $student) : ?>
        <tr>
          <th><?php echo $student ?></th>
          <?php foreach ($course_subjects as $i => $course): ?>
            <?php include_partial('student_course_result_marks', array(
              'student' => $student,  
              'course_subject' => $course_subjects[$i])) ?>
          <?php endforeach; ?>
          <td><?php echo  count(StudentRepprovedCourseSubjectPeer::retrieveByStudentAndCareer($student, $division->getCareerSchoolYear()->getCareer()))?></td>
        <?php endforeach; ?>
      </tr>
    </tbody>
  </table>