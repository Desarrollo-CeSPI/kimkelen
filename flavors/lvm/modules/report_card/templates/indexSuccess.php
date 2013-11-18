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
<?php use_stylesheet('/css/report-card.css') ?>
<?php use_stylesheet('/css/print-report-card.css', '', array('media' => 'print')) ?>
<?php use_helper('Asset', 'I18N') ?>

<div class="non-printable">
  <div><a href="<?php echo url_for('@export_report_cards?sf_format=pdf&orientation=landscape&font-family=freemono') ?>"><?php echo __('Export report cards') ?></a></div>
  <div><a href="javascript:history.back(1)"><?php echo __('Go back') ?></a></div>
</div>

<?php foreach ($students as $student): ?>
  <?php $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $division->getCareerSchoolYear()) ?>
  <div class="report-wrapper">
    <?php include_partial('header', array('student' => $student, 'division' => $division, 'career_id' => $career_id,'school_year'=>$student_career_school_year->getSchoolYear(), 'student_career' => CareerStudentPeer::retrieveByCareerAndStudent($career_id, $student->getId()))); ?>

    <div class="report-content">
      <?php if ($student->hasCourseType(CourseType::TRIMESTER, $student_career_school_year)): ?>
        <?php $periods = CareerSchoolYearPeriodPeer::getTrimesterPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>
        <?php if ($course_subject_students_attendance_day = $student->getCourseSubjectStudentsForCourseTypeAndAttendanceForDay(CourseType::TRIMESTER, $student_career_school_year)): ?>
          <?php include_partial('course_subject_trimester', array('student' => $student, 'course_subject_students' => $course_subject_students_attendance_day, 'periods' => $periods, 'has_attendance_for_subject' => false, 'student_career_school_year' => $student_career_school_year)) ?>
        <?php endif ?>

        <?php if ($division->getYear() == 4): ?>
          <?php $introduccion = SchoolBehaviourFactory::getEvaluatorInstance()->getCourseSubjectStudentsForIntroduccion($student, $division->getCareerSchoolYear()) ?>
          <?php include_partial('introduccion', array('student' => $student, 'course_subject_students' => $introduccion, 'division' => $division, 'student_career_school_year' => $student_career_school_year)) ?>
        <?php endif ?>
        <?php ?>

        <?php if ($course_subject_student_attendance_subject = $student->getCourseSubjectStudentsForCourseTypeAndAttendanceForSubject(CourseType::TRIMESTER, $student_career_school_year)): ?>
          <?php if ($division->getYear() == 4): ?>
            <?php $course_subject_student_attendance_subject = array_udiff($course_subject_student_attendance_subject, $introduccion, array('CourseSubjectStudent', 'compare')); ?>
          <?php endif ?>
          <?php include_partial('course_subject_trimester', array('student' => $student, 'course_subject_students' => $course_subject_student_attendance_subject, 'periods' => $periods, 'has_attendance_for_subject' => true, 'student_career_school_year' => $student_career_school_year)) ?>
        <?php endif ?>
      <?php endif; ?>

      <?php if ($student->hasCourseType(CourseType::QUATERLY, $student_career_school_year)): ?>
        <?php $periods = CareerSchoolYearPeriodPeer::getQuaterlyPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>
        <?php if ($course_subject_students_attendance_day = $student->getCourseSubjectStudentsForCourseTypeAndAttendanceForDay(CourseType::QUATERLY, $student_career_school_year)): ?>
          <?php include_partial('course_subject_quaterly', array('student' => $student, 'course_subject_students' => $course_subject_students_attendance_day, 'periods' => $periods, 'has_attendance_for_subject' => false, 'student_career_school_year' => $student_career_school_year)) ?>
        <?php endif ?>
        <?php if ($course_subject_student_attendance_subject = $student->getCourseSubjectStudentsForCourseTypeAndAttendanceForSubject(CourseType::QUATERLY, $student_career_school_year)): ?>
          <?php include_partial('course_subject_quaterly', array('student' => $student, 'course_subject_students' => $course_subject_student_attendance_subject, 'periods' => $periods, 'has_attendance_for_subject' => true, 'student_career_school_year' => $student_career_school_year)) ?>
        <?php endif ?>
      <?php endif; ?>

      <?php if ($student->hasCourseType(CourseType::BIMESTER, $student_career_school_year)): ?>
        <?php $periods = CareerSchoolYearPeriodPeer::getBimesterPeriodsSchoolYear($division->getCareerSchoolYearId()); ?>
        <?php include_partial('course_subject_bimester', array('student' => $student, 'periods' => array_chunk($periods, 2), 'division' => $division, 'student_career_school_year' => $student_career_school_year)) ?>
      <?php endif; ?>

      <?php if (!is_null($average = $student_career_school_year->getAnualAverage())): ?>
        <?php include_partial('average', array('average' => $average)); ?>
      <?php endif; ?>

      <?php include_partial('footer', array('student' => $student, 'division' => $division)); ?>
    </div>
  </div>
  <div style="clear:both;"></div>
  <div style="page-break-before: always;"></div>
<?php endforeach; ?>