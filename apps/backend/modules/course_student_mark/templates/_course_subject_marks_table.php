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
<?php use_stylesheet('print_table.css', 'last', array('media' => 'print')) ?>

<?php foreach ($course_subjects as $course_subject): ?>
  <div class="print_page">
    <?php $career = $course_subject->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getCareer(); ?>
    <?php $course = $course_subject->getCourse(); ?>
    <?php $final_period = $course_subject->isFinalPeriod(); ?>
    <?php $configuration = $course_subject->getCareerSubjectSchoolYear()->getConfiguration() ?>

    <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_students">
      <div>
        <table  width="100%" border="0" cellspacing="1" cellpadding="2" class="tbl_content" id="export_to_excel">
          <thead>
            <?php include_partial("course_student_mark/thead",array('course' => $course, "configuration" => $configuration));?>
          </thead>          

          <tbody class="print_body">
            <?php include_partial("course_student_mark/tbody", array('course' => $course, "configuration" => $configuration, "course_subject" => $course_subject, "final_period" => $final_period));?>
            <?php include_partial("course_student_mark/tfoot", array('configuration' => $configuration, "course_subject" => $course_subject));?>
          </tbody>
          
        </table>
        </div>
      <div style="margin-top: 1px; clear: both;"></div>
    </div>
  </div>
<?php endforeach ?>

<form action="<?php echo url_for('@print_table')?>" method="post" target="_blank" id="exportation_form">
  <p><?php echo __('Export to excel') ?> <?php echo image_tag('export_to_excel.gif', array('class' => 'excel_button')) ?></p>
  <input type="hidden" id="send_data" name="send_data" />
  <a href="<?php echo url_for('course_student_mark/goBack') ?>"><?php echo __('Go back') ?></a>
</form>

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