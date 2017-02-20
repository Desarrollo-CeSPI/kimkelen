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

<div class="row">
  <div class="col-md-12">
    <?php include_partial('mainFrontend/personal_info', array('person' => $student)) ?>

    <div class="col-md-8">
      <div class="row title-box">
        <div class="col-md-12 title-icon">
          <?php echo image_tag("frontend/notepad.svg", array('alt' => __('Califications'))); ?>
          <span class="title-text"> <?php echo __("Califications report");?> </span>
        </div>
      </div>

      <div class="row action-box">
        <div class="col-md-12 text-right">
          <?php echo link_to(
            '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>' . __('Go back') .'',
            '@homepage',
            array('class' => 'btn btn btn-primary')
          )?>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <ul class="nav nav-tabs" role="tablist">
          <?php $last_year= $student->getLastStudentCareerSchoolYear()->getCareerSchoolYear()->getSchoolYear()->getYear();?>
          <?php foreach ($student->getStudentCareerSchoolYears() as $student_career_school_year): ?>
            <?php $year= $student_career_school_year->getCareerSchoolYear()->getSchoolYear()->getYear(); ?>
            <?php $class= ($student_career_school_year->getCareerSchoolYear()->getSchoolYear()->getIsActive() || ($year == $last_year))?'active' : ''; ?>
            <li role="presentation"class="<?php echo $class?>" ><a href="<?php echo '#' . $year ?>" aria-controls="home" role="tab" data-toggle="tab"><?php echo $year ?></a></li>
          <?php endforeach ?>
          </ul>

          <div class="data-box">
          <?php foreach ($student->getStudentCareerSchoolYears() as $student_career_school_year): ?>
            <?php $year= $student_career_school_year->getCareerSchoolYear()->getSchoolYear()->getYear(); ?>
            <?php $class= ($student_career_school_year->getCareerSchoolYear()->getSchoolYear()->getIsActive() || ($year == $last_year))?'tab-pane active' : 'tab-pane'; ?>
            <div role="tabpanel" class="<?php echo $class ?>" id="<?php echo $year ?>">
            <?php $divisions = $student->getCurrentDivisions($student_career_school_year->getCareerSchoolYearId())?>

            <div class="status">
              <strong><?php echo __('Year %%year%%',array('%%year%' => $student_career_school_year->getYear()))?></strong>
              <strong><?php echo __("Status") .': '?></strong> <?php echo $student_career_school_year->getStatusString() ?>
            </div>

            <?php $career_school_year = $student_career_school_year->getCareerSchoolYear(); ?>
            <?php $course_subject_students = $student_career_school_year->getCourses(); ?>
            <?php $career_student = CareerStudentPeer::retrieveByCareerAndStudent($career_school_year->getCareerId(), $student->getId()) ?>
            <?php
            isset($course_subject_students['ANUAL']) ? include_partial("califications/current_course_subjects", array("course_subject_students" => $course_subject_students['ANUAL'],
            'career_student' => $career_student,
            'student' => $student,
            'course_type' => CourseType::TRIMESTER)) : ''
            ?>

            <?php
            isset($course_subject_students['QUATERLY']) ? include_partial("califications/current_course_subjects", array("course_subject_students" => $course_subject_students['QUATERLY'],
            'career_student' => $career_student,
            'student' => $student,
            'course_type' => CourseType::QUATERLY)) : ''
            ?>

            <?php
            isset($course_subject_students['BIMESTER']) ? include_partial("califications/current_course_subjects", array("course_subject_students" => $course_subject_students['BIMESTER'],
            'career_student' => $career_student,
            'student' => $student,
            'course_type' => CourseType::BIMESTER)) : ''
            ?>

            <?php
            isset($course_subject_students['QUATERLY_OF_A_TERM']) ? include_partial("califications/current_course_subjects", array("course_subject_students" => $course_subject_students['QUATERLY_OF_A_TERM'],
            'career_student' => $career_student,
            'student' => $student,
            'course_type' => CourseType::QUATERLY_OF_A_TERM)) : ''
            ?>

            <?php if ($anual_average = $student_career_school_year->getAnualAverage()): ?>

              <div class="info_div">
                <strong><?php echo __("Anual average") ?></strong> <em><?php echo $anual_average ?></em>
              </div>
            <?php endif; ?>
            </div>
          <?php endforeach ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
