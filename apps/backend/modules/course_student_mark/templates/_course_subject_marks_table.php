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
  <span><a href="#" onclick="window.print(); return false;"><?php echo __('Print') ?></a></span>
  <span><a href="<?php echo url_for('course_student_mark/goBack') ?>"><?php echo __('Go back') ?></a></span>
  <form action="<?php echo url_for('@print_table') ?>" method="post" target="_blank" id="exportation_form">
    <p><?php echo __('Export to excel') ?> <?php echo image_tag('export_to_excel.gif', array('class' => 'excel_button')) ?></p>
    <input type="hidden" id="send_data" name="send_data" />
  </form>
</div>

<div class="report-wrapper"  id="export_to_excel">
  <?php foreach ($course_subjects as $course_subject): ?>
    <?php $career = $course_subject->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getCareer(); ?>
    <?php $course = $course_subject->getCourse(); ?>
    <?php $final_period = $course_subject->isFinalPeriod(); ?>
    <?php $configuration = $course_subject->getCareerSubjectSchoolYear()->getConfiguration() ?>

    <div class="report-header">
      <div class="logo"><?php echo image_tag("kimkelen_logo.png", array('absolute' => true)) ?></div>
      <div class="header_row">
        <h2><?php echo __('Print califications'); ?></h2>
        <div class="title"><?php echo __('School year') ?>: </div>
        <div class="orientation"><?php echo $course->getSchoolYear() ?></div>
        <div class="title"><?php echo __('Año/Nivel') ?>: </div>
        <div class="course"><?php echo $course->getYear() ?></div>
        <?php if (!(is_null($course->getDivision()))): ?>
          <div class="title"><?php echo __('Division') ?>: </div>
          <div class="course"><?php echo $course->getDivision()->getDivisionTitle(); ?></div>
        <?php endif; ?>
        <div class="title"><?php echo __('Subject') ?>: </div>
        <div class="orientation"><?php echo $course->getSubjectsStr(); ?></div>
      </div>
      <div class="header_row">
        <div class="title"><?php echo __('Teacher') ?>: </div>
        <div class="orientation"><?php echo $course->getTeachersStr() ?></div>
      </div>
    </div>

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
  <span><a href="#" onclick="window.print(); return false;"><?php echo __('Print') ?></a></span>
  <span><a href="<?php echo url_for('course_student_mark/goBack') ?>"><?php echo __('Go back') ?></a></span>
  <form action="<?php echo url_for('@print_table') ?>" method="post" target="_blank" id="exportation_form">
    <p><?php echo __('Export to excel') ?> <?php echo image_tag('export_to_excel.gif', array('class' => 'excel_button')) ?></p>
    <input type="hidden" id="send_data" name="send_data" />
  </form>
</div>

<script language="javascript">
  jQuery(document).ready(function()
  {
    jQuery(".excel_button").click(function(event)
    {
      jQuery("#send_data").val( jQuery("<div>").append( jQuery("#export_to_excel").eq(0).clone()).html());
      jQuery("#exportation_form").submit();
    });
  });
</script>