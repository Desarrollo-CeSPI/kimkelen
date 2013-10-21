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
<?php use_stylesheet('report-card.css', 'first', array('media' => 'screen')) ?>
<?php use_stylesheet('print-report-card.css', 'last', array('media' => 'print')) ?>
<?php use_stylesheet('main.css', '', array('media' => 'all')) ?>

<div class="non-printable">
  <span><a href="<?php echo url_for('course_student_mark/goBack') ?>"><?php echo __('Go back') ?></a></span>
  <span><a href="#" onclick="window.print(); return false;"><?php echo __('Print') ?></a></span>
  <form action="<?php echo url_for('@print_table') ?>" method="post" target="_blank" id="exportation_form">
    <input type="hidden" id="send_data" name="send_data" />
  </form>
  <span><a href="#" onclick="javascript:exportToExcel()"><?php echo __('Export to excel') ?></a></span>
</div>

<div class="report-wrapper"  id="export_to_excel">
  <?php foreach ($course_subjects as $course_subject): ?>
    <?php $career = $course_subject->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getCareer(); ?>
    <?php $course = $course_subject->getCourse(); ?>
    <?php $final_period = $course_subject->isFinalPeriod(); ?>
    <?php $configuration = $course_subject->getCareerSubjectSchoolYear()->getConfiguration() ?>

    <?php include_partial('information_header', array('course'=> $course)); ?>

    <div style="clear:both"></div>
    <table width="100%" class="gridtable_bordered">
      <?php include_partial("course_student_mark/thead", array('course' => $course, "configuration" => $configuration)); ?>
      <tbody class="print_body">
        <?php include_partial("course_student_mark/tbody", array('course' => $course, "configuration" => $configuration, "course_subject" => $course_subject, "final_period" => $final_period)); ?>
      </tbody>
    </table>
  </div>

  <div class="report-wrapper">
    <?php if ($configuration->getCourseType() == CourseType::TRIMESTER): ?>
      <?php include_partial('trimester_boxes', array('marks_count' => $configuration->getCourseMarks())); ?>
    <?php elseif ($configuration->getCourseType() == CourseType::QUATERLY): ?>
      <?php include_partial('quaterly_boxes', array('marks_count' => $configuration->getCourseMarks())); ?>
    <?php elseif ($configuration->getCourseType() == CourseType::BIMESTER): ?>
      <?php include_partial('bimester_boxes', array('marks_count' => $configuration->getCourseMarks())); ?>
    <?php elseif ($configuration->getCourseType() == CourseType::QUATERLY_OF_A_TERM): ?>
      <?php include_partial('quaterly_of_a_term_boxes'); ?>
    <?php endif; ?>
  </div>
<?php endforeach; ?>

<div style="clear:both"></div>
<div class="non-printable">
  <span><a href="<?php echo url_for('course_student_mark/goBack') ?>"><?php echo __('Go back') ?></a></span>
  <span><a href="#" onclick="window.print(); return false;"><?php echo __('Print') ?></a></span>
  <form action="<?php echo url_for('@print_table') ?>" method="post" target="_blank" id="exportation_form">
    <input type="hidden" id="send_data" name="send_data" />
  </form>
  <span><a href="#" onclick="javascript:exportToExcel()"><?php echo __('Export to excel') ?></a></span>
</div>

<script language="javascript">

  function exportToExcel(){
    jQuery("#send_data").val( jQuery("<div>").append( jQuery("#export_to_excel").eq(0).clone()).html());
    jQuery("#exportation_form").submit();
  };
</script>