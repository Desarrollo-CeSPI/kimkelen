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
        <?php foreach ($career_subjects as $i => $career_subject) : ?>
          <th colspan="<?php echo $configurations[$i]->getCourseMarks() + 1 ?>"><?php echo $career_subject->getSubject()->getFantasyName(); ?></th>
        <?php endforeach; ?>
          <th rowspan="2">Prom general</th>
      </tr>
      <tr>
        <?php foreach ($configurations as $i => $dconfiguration) : ?>
          <?php for ($j = 1; $j <= $dconfiguration->getCourseMarks(); $j++): ?>
            <th class="mark"><?php echo SchoolBehaviourFactory::getInstance()->getMarkNameByNumberAndCourseType($j, $dconfiguration->getCourseType()); ?></th>
          <?php endfor; ?>
          <th class="mark">Prom</th>
        <?php endforeach; ?>
          
      </tr>
    </thead>
    <tbody class="print_body">
      <?php $school_year = $division->getSchoolYear();?>
      <?php $career = $division->getCareer();?>
      <?php $csy = CareerSchoolYearPeer::retrieveByCareerAndSchoolYear($career,$school_year); ?>
      <?php foreach ($students as $student) : ?>
        <tr>
          <th><?php echo $student ?></th>
          <?php foreach ($courses as $i => $course): ?>
            <?php include_component('division', 'student_marks', array(
              'student' => $student, 
              'career_subject_school_year' => $career_subject_school_years[$i], 
              'course_subject' => $course_subjects[$i])) ?>
          <?php endforeach; ?>
          <?php $student_career_school_year = StudentCareerSchoolYearPeer::retrieveByStudentAndCareerSchoolYear($student, $csy);?>
          
          <td><?php echo $student_career_school_year->getAnualAverage();?></td>
        <?php endforeach; ?>
          
      </tr>
    </tbody>
  </table>