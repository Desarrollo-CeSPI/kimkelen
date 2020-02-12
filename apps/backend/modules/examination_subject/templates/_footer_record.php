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

  
<?php $evaluator_instance = SchoolBehaviourFactory::getEvaluatorInstance();?>
<div class="article-div">
    <strong>Art 34º: </strong><span class="sub">Exámenes regulares:</span> La evaluación en las mesas de exámenes regulares, regulares complementarios o regulares previos, se realizará sobre aquellos contenidos desarrollados durante el ciclo lectivo cursado.
  </div>

  <div class="article-div">
    <strong>Art 35º: </strong><span class="sub">Exámenes libres:</span> La evaluación en las mesas de exámenes de alumnos libres, se realizará sobre aquellos contenidos del programa del ciclo lectivo cursado de acuerdo a las reglamentaciones vigentes.
  </div>

  <div class="article-div">
    <strong>En ambos casos: </strong>"La evaluación podrá ser oral y/o escrita y/o práctica. Siempre que se utilice más de una modalidad, éstas no podrán ser eliminatorias entre sí, debiendo tener un carácter complementario.
  </div>
  <div class="article-div">
    <div class="observation-box">
      <strong><?php echo __('Observations'); ?></strong>:
    </div>
  </div>
  <div class="article-div">
    <p>La Mesa Examinadora para la evaluación de los alumnos inscriptos en la presente acta, ha utilizado la modalidad (marcar lo que corresponda)</p>
    <div>
      <span><?php echo __('Oral'); ?>:</span>
      <span class="little-box"></span>
      <span><?php echo __('Escrita'); ?>:</span>
      <span class="little-box"></span>
      <span><?php echo __('Práctica'); ?>:</span>
      <span class="little-box"></span>
    </div>
  </div>

    <div class="article-div">
      <strong><?php echo __('Total de alumnos'); ?>:</strong>
      <span class="little-box">
        <?php echo  $record->countRecordDetailsForSheet($rs->getSheet()) ?>
      </span>

      <strong><?php echo __('Aprobados'); ?>:</strong>
      <?php if($examination_subject->getIsClosed()) : ?>
        <span class="little-box">
          <?php echo $record->countRecordDetailsForSheetAndResult($rs->getSheet(),$evaluator_instance->getApprovedResult())?>
        </span>
      <?php else: ?>
      <span class="little-box"></span>
      <?php endif; ?>
      
      <strong><?php echo __('Aplazados'); ?>:</strong>
      <?php if($examination_subject->getIsClosed()) : ?>
      <span class="little-box">
        <?php echo ($examination_subject->getIsClosed()) ? $record->countRecordDetailsForSheetAndResult($rs->getSheet(),$evaluator_instance->getDisapprovedResult()) : '' ?>
      </span>
      <?php else: ?>
      <span class="little-box"></span>
      <?php endif; ?>
      
      <strong><?php echo __('Ausentes'); ?>:</strong>
      <?php if($examination_subject->getIsClosed()) : ?>
      <span class="little-box">
        <?php echo  ($examination_subject->getIsClosed()) ? $record->countRecordDetailsForSheetAndResult($rs->getSheet(),$evaluator_instance->getAbsentResult()) : '' ?>
      </span>
      <?php else: ?>
      <span class="little-box"></span>
      <?php endif; ?>
    </div>

  <div class="record-footer">
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
      <div class="sheet-record">
          <span>
              Hoja <?php echo $rs->getSheet() . '/' . count($record->getRecordSheets())?>
          </span>
      </div>
  </div>