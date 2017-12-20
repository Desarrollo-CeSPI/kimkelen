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
<div class="report-header">
    <div class="logo"><?php echo image_tag("kimkelen_logo.png", array('absolute' => true)) ?></div>
    <div class="header_row">
      <h2><?php echo __('Print averages'); ?></h2>
      <div class="title2"><?php echo __('Year '. $year) .' ' .$school_year  ?>  </div>
      <div class="row-content"></div>
    </div>
</div>

    <div style="clear:both"></div>
    <table width="100%" class="gridtable_bordered">
      <tr class="head" valign="bottom">
        <td align="center"  height="22" colspan="2"><?php echo SchoolBehaviourFactory::getInstance()->getSchoolName()?></td>
        <td align="center"   height="22" colspan="8">Planilla de promedios <?php echo $school_year ?></td>
      </tr>
      <tr class="head" valign="bottom">
        <td align="center"  height="22" colspan="2"></td>
        <td align="center" colspan="8" >Promedios</td>
      </tr>
      <tr class="head" valign="bottom">
          <td align="center"  height="22" colspan="1"></td>
          <td align="center"  height="22" colspan="1"><strong><?php echo __('Nombre y Apellido'); ?></strong></td>
        <?php for($i= 1;$i <= $year; $i ++): ?>
          <td align="center" height="22" ><strong><?php echo __('Year ' . $i); ?></strong></td>
        <?php endfor;?>
          <td align="center" height="22"><strong><?php echo __('Average'); ?></strong></td>
      </tr>
      <tbody class="print_body">
            <?php $j = 0; ?>
            <?php foreach ($students as $student):?>
            <?php $j++; ?>
            <tr>
                <td><?php echo $j; ?></td>
                <td style="text-align: left"><?php echo $student ?></td>
                <?php $sum = 0; $count = 0;?>
                <?php for($i= 1;$i <= $year; $i ++): ?>
                <td>
                    <?php $scsy = StudentCareerSchoolYearPeer::retrieveByStudentAndYear($student, $i); 
                    if(!is_null($scsy))
                    {
                        $prom = $scsy->getAnualAverageWithDisapprovedSubjects();
                        $sum += $prom;
                        $count ++;
                        echo $prom;
                    }
                    else
                    {
                      echo '-';   
                    }
                    ?>
                    
                    <?php unset($prom);?>
                    <?php unset($scsy);?>
                </td>
                <?php endfor;?>
                <td>
                    <strong><?php echo ($sum != 0 && $count != 0)? number_format(round($sum / $count, 3), 3, ',', '') : '';?>  </strong> 
                </td>
            </tr>
            <?php           
             $student->clearAllReferences(true); 
             unset($student);
            ?>
            
            <?php endforeach?>
            <?php unset($students);?>
      </tbody>    
    </table>

  <br><div style="clear:both"></div><br>
  <div style="page-break-before: always;"></div>

 </div>

<script language="javascript">

  function exportToExcel(){
    jQuery("#send_data").val( jQuery("<div>").append( jQuery("#export_to_excel").eq(0).clone()).html());
    jQuery("#exportation_form").submit();
  };
</script>