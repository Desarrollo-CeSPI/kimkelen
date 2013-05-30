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
<?php #use_helper('I18N', 'Date', 'JavascriptBase')        ?>


<table>
  <thead>
  <th>
    <?php echo __('Materias') ?>
  </th>
  <th>
    <?php echo __('Cantidad de notas') ?>
  </th>
  <th>
    <?php echo __('Tipo de materia') ?>
  </th>
  <th>
    <?php echo __('Tipo de Asistencia') ?>
  </th>


</thead>


<tbody>

  <?php foreach ($division->getCourses() as $course): ?>
    <tr>
      <td>   <?php echo $course ?></td>
      <?php /* @var $career_subject_school_year CareerSubjectSchoolYear */ ?>
      <?php foreach ($course->getCourseSubjects() as $course_subject): ?>

        <?php $career_subject_school_year = $course_subject->getCareerSubjectSchoolYear(); ?>
        <td> <?php echo $career_subject_school_year->getConfiguration()->getCourseMarks() ?> </td>
        <td> <?php echo CourseType::getOption($career_subject_school_year->getConfiguration()->getCourseType()) ?> </td>
        <?php if ($career_subject_school_year->getConfiguration()->getAttendanceType() == AbsenceMethod::SUBJECT): ?>
          <td><? echo __('Per day'); ?></td>

        <?php else: ?>
          <td> <?php echo __('Per subject'); ?>
            <ul>
              <?php if ($course_subject->getCourseSubjectConfigurations()): ?>
                <?php foreach ($course_subject->getCourseSubjectConfigurations() as $configuration): ?>
                  <li> <?php echo $configuration->getPeriod()->getShortName() . ' = ' . $configuration->getMaxAbsence() ?> </li>
                <?php endforeach; ?>
              <?php else: ?>
                  <li>falta configurar</li>
              <?php endif ?>
            </ul>



          </td>
        <?php endif ?>




        <?php #echo EvaluationMethod::getOption($career_subject_school_year->getConfiguration()->getEvaluationMethod())?>

        <?php #echo $career_subject_school_year->getSubjectConfiguration()->getAttendanceType()?>


      <?php endforeach; ?>
    </tr>
  <?php endforeach; ?>
</tbody>
</table>