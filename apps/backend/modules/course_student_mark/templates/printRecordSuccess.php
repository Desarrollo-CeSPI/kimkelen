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
<?php use_stylesheet('examination-record.css') ?>
<?php use_stylesheet('print-examination-record.css', 'last', array('media' => 'print')) ?>

<?php $evaluator_instance = SchoolBehaviourFactory::getEvaluatorInstance();?>
<?php foreach ($record->getRecordSheets() as $rs):?>
<div class="record-wrapper">
  <div class="record-header">
    <div>
      <div class="logo"><?php echo image_tag("kimkelen_logo.png", array('absolute' => true)) ?></div>
    </div>
  </div>  
  <div style="text-align: center">
      <h2>Acta de <?php echo ($cs->getCourse()->isPathway())? 'Trayectoria' : 'Cursada'?></h2>
  </div>

  <div class="gray-background">
      <strong><?php echo __('Subject'); ?></strong>:
      <strong><?php echo $cs->getCareerSubjectSchoolYear()->getCareerSubject()->getSubject() . ' - ' . $cs->getCareerSubjectSchoolYear()->getYear() . ' año'  ?></strong>
      
      <span class="right">
          <strong><?php echo __('School year'); ?></strong>: <?php echo $cs->getCourse()->getSchoolYear() ?> 
  </div>
  <br>
  <div class="gray-background">
    <span><strong><?php echo 'Acta N°: '; ?></strong>  <?php echo $record->getId(); ?> </span>
    <span class="right"><strong><?php echo 'Tomo: '; ?></strong><?php echo $rs->getBook(); ?>     <strong> <?php echo 'Folio físico: '; ?></strong><?php echo $rs->getPhysicalSheet(); ?></span>
  </div>
  <br>
  <table class="gridtable_bordered">
    <thead>
      <tr class="printColumns">
        <th rowspan="2"><?php echo __('N°'); ?> </th>
        <th rowspan="2"><?php echo __('Apellido y Nombre'); ?></th>
        <th colspan="2"><?php echo __('Mark'); ?></th>
        <th rowspan="2"><?php echo __('Resultado'); ?></th>
      </tr>
      <tr>
        <th><?php echo __('Números'); ?></th>
        <th><?php echo __('Letras'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; ?>
      <?php foreach ($record->getRecordDetailsForSheet($rs->getSheet()) as $rd): ?>
        <tr>
          <td class="orden"><?php echo $rd->getLine() ?> </td>
          <td class="student" style="text-align: left"><?php echo $rd->getStudent() ?></td>
          <td class="calification number"><?php echo ($rd->getMark())? $rd->getMark(): '-'; ?></td>
          <td class="calification text">
            <?php $c = new num2text();?>
            <?php if(!$rd->getIsAbsent()):?>
              
              <?php $c = new num2text();
                $mark = $rd->getMark();
                $mark_parts = explode(',', $mark);
                
                if (1 === count($mark_parts))
                {
                    $mark_parts = explode('.', $mark);    
                }
                $mark_symbol = (1 === count($mark_parts)) ? trim($c->num2str($mark_parts[0])) :trim($c->num2str($mark_parts[0])) . ('00' !== $mark_parts[1]?','.$mark_parts[1]:''); ?>

               <?php echo $mark_symbol; ?>
              <?php else: ?>
                <?php echo __('Absent'); ?>
              <?php endif; ?>
          </td>
          <td> <?php echo $evaluator_instance->getResultStringFor($rd->getResult()) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <br>
  <div class="article-div">
    <div class="observation-box">
      <strong><?php echo __('Observations'); ?></strong>:
    </div>
  </div>
  <div>
    <div class="article-div">
      <strong><?php echo __('Total de alumnos'); ?>:</strong>
      <span class="little-box">
        <?php echo $record->countRecordDetailsForSheet($rs->getSheet()) ?>
      </span>

      <strong><?php echo __('Aprobados'); ?>:</strong>
      <span class="little-box">
        <?php echo $record->countRecordDetailsForSheetAndResult($rs->getSheet(),$evaluator_instance->getApprovedResult()) ?>
      </span>

      <strong><?php echo __('Aplazados'); ?>:</strong>
      <span class="little-box">
        <?php echo $record->countRecordDetailsForSheetAndResult($rs->getSheet(),$evaluator_instance->getDisapprovedResult()) ?>
      </span>

      <strong><?php echo __('Ausentes'); ?>:</strong>
      <span class="little-box">
        <?php echo $record->countRecordDetailsForSheetAndResult($rs->getSheet(),$evaluator_instance->getAbsentResult()) ?>
      </span>
    </div>
  </div>
  <br>
  <div class="article-div">
    La Plata, __________ de ______________________ de __________
  </div>

  <div style="margin-left: 25px;" class="signature">
    <p class="signature-text">Vocal</p>
    <p class="signature-subtext">Firma y aclaración</p>
  </div>

  <div class="signature">
    <p class="signature-text">Presidente</p>
    <p class="signature-subtext">Firma y aclaración</p>
  </div>

  <div class="signature">
    <p class="signature-text">Vocal</p>
    <p class="signature-subtext">Firma y aclaración</p>
  </div>
  
  <div>
      <span class="right min-size">
          Hoja <?php echo $rs->getSheet() . '/' . count($record->getRecordSheets())?>
      </span>
  </div>

</div>
<div style="page-break-before: always;"></div>
<?php endforeach;?>