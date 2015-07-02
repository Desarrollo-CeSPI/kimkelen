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
<?php use_stylesheet('print.css', 'last', array('media' => 'print')) ?>
<?php use_javascript('fix/jquery.dataTables.js') ?>
<?php use_javascript('fix/FixedColumns.js') ?>

<ul style="list-style: none">
  <li> 
    <a href="<?php echo url_for('division/exportCalificationTable') . '?id='.$division->getId();?>" class="excel_button"><?php echo __('Export to excel') ?>
      <?php echo image_tag('export_to_excel.gif', array('class' => 'excel_button')) ?>
    </a>
  </li>
  <li class="sf_admin_action_list">
    <a href="<?php echo url_for('division/index') ?>"><?php echo __('Go back') ?></a>
  </li>
</ul>

<h1 class="print_title"><?php echo $division; ?></h1>          
<div id="mi_tabla_wrapper">
  <?php include_partial('division/calification_table', array(
      'career_subjects' => $career_subjects,
      'configurations'  => $configurations,
      'students'        => $students,
      'courses'         => $courses,
      'course_subjects' => $course_subjects,
      'career_subject_school_years' => $career_subject_school_years,
  ));?>
</div>

<?php if (count($career_subjects) > 8): ?>
  <script type="text/javascript">
    jQuery(document).ready(function()
    {
      var oTable = jQuery('#export_to_excel').dataTable( {
        "sScrollX": "100%",
        "bPaginate": false,
        "bFilter": false,
        "bAutoWidth": false ,
        "aoColumnDefs": [ { "sWidth": "30px%", "aTargets": [ '_all' ] }]
      });

      new FixedColumns( oTable, {
        "sLeftWidth": 'relative',
        "iLeftWidth": 16
      });
    });
  </script>
<?php endif ?>