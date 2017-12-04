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
  <span><a href="<?php echo url_for('division') ?>"><?php echo __('Go back') ?></a></span>
  <span><a href="#" onclick="window.print(); return false;"><?php echo __('Print') ?></a></span>
  <form action="<?php echo url_for('@print_table') ?>" method="post" target="_blank" id="exportation_form">
    <input type="hidden" id="send_data" name="send_data" />
  </form>
  <span><a href="#" onclick="javascript:exportToExcel()"><?php echo __('Export to excel') ?></a></span>
</div>

<div class="report-wrapper report-average"  id="export_to_excel">
   <?php include_partial('header',array('division' => $division));?>

    <div style="clear:both"></div>
    <table width="100%" class="gridtable_bordered">
      <tr class="head" valign="bottom">
        <td align="center" width="60%" height="22" colspan="2"><?php echo SchoolBehaviourFactory::getInstance()->getSchoolName()?></td>
        <td align="center" width="40%" height="22" colspan="2">Planilla de promedios <?php echo $division->getSchoolYear()?></td>
      </tr>
      <tr class="head" valign="bottom">
        <td align="center" width="60%" height="22" colspan="2"></td>
        <td align="center" width="40%" colspan="2">Promedio Anual</td>
      </tr>
      <tr class="head" valign="bottom">
        <td align="center" width="5%" height="22"></td>
        <td align="center" width="55%" height="22"><?php echo __('Nombre y Apellido'); ?></td>
        <td align="center" width="10%" height="22" ><?php echo __('N'); ?></td>
        <td align="center" width="30%" height="22"><?php echo __('Letras'); ?></td> 
      </tr>
     <tbody class="print_body">
        <?php $i = 0; ?>
          <?php foreach ($students as $student): ?>
            <?php $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($student, $division->getCareerSchoolYear());?>
            <?php $i++ ?>
            
            <tr>
              <td><?php echo $i ?></td>
              <td style="text-align: left"><?php echo $student ?></td>
              <td align="center"><?php echo ($student_career_school_year->getAnualAverage())? $student_career_school_year->getAnualAverage() : ''; ?></td>
              <?php $c = new num2text(); ?>
	      <td><?php echo ($student_career_school_year->getAnualAverage() )? $c->num2str($student_career_school_year->getAnualAverage()) : '' ?></td>
            </tr>
          
        <?php endforeach ?>
      </tbody>    
    </table>

  <br><div style="clear:both"></div><br>
  <div style="page-break-before: always;"></div>

 </div>
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