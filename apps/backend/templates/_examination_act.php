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

<div class="non-printable">
  <a href="<?php echo url_for('manual_examination_subject') ?>"><?php echo __('Go back') ?></a>
  <a href="#" onclick="window.print(); return false;"><?php echo __('Print') ?></a>
</div>

<div class="record-wrapper">
  <div class="record-header">
    <div>
      <div class="logo"><?php echo image_tag("kimkelen_logo.png", array('absolute' => true)) ?></div>
    </div>
      <div style="float:right">
          <h2>Acta volante de Exámenes</h2>
      </div>


  </div>

  <div class="article-div">
    <div><?php echo __('Day') ?> _____ / _____ / _____ &nbsp;<?php echo __('Hora') ?> _____ : _____ </div>
  </div>

  <div class="gray-background">
    <span><strong><?php echo 'Exámenes de Alumnos'; ?></strong>:
      <strong>
        <?php echo $examination_subject->getExamination(); ?>
      </strong>
    </span>

  </div>
  <br>
  <div class="gray-background">
    <span><strong><?php echo __('Subject'); ?></strong>:
      <strong><?php echo $examination_subject->getSubject() . ' - ' . $examination_subject->getYear() . ' año'  ?></strong>
      <span class="right"><strong><?php echo __('School year'); ?></strong>: <?php echo $examination_subject->getExamination()->getSchoolYear() ?></span>
  </div>
  <br>
  <table class="gridtable_bordered">
    <thead>
      <tr class="printColumns">
        <th rowspan="2"><?php echo __('N° de Orden'); ?> </th>
        <th rowspan="2"><?php echo __('Apellido y Nombre'); ?></th>
        <th rowspan="2"><?php echo __('División'); ?></th>
        <th colspan="2"><?php echo __('Mark'); ?></th>
      </tr>
      <tr>
        <th><?php echo __('Números'); ?></th>
        <th><?php echo __('Letras'); ?></th>
      </tr>
    </thead>

    <tbody>
      <?php $i = 1; ?>
      <?php foreach ($students as $student): ?>
        <tr>
          <td class="orden"><?php echo $i ?> </td>
          <td class="student"><?php echo $student ?> </td>
          <td class="division"><?php echo implode(', ', DivisionPeer::retrieveStudentSchoolYearDivisions($examination_subject->getSchoolYear(), $student)); ?> </td>
          <?php $ess = $examination_subject->getExaminationNoteForStudent($student); ?>
          <td class="calification number">
            <?php if ($examination_subject->getIsClosed()): ?>
              <?php if (!$ess->getIsAbsent()): ?>
                <?php echo $ess->getMark() ?>
              <?php endif; ?>
            <?php endif; ?>
          </td>
          <td class="calification text">
            <?php if ($examination_subject->getIsClosed()): ?>
              <?php if (!$ess->getIsAbsent()): ?>
                <?php echo $ess->getMarkText() ?>
              <?php else: ?>
                <?php echo __('Absent'); ?>
              <?php endif; ?>
            <?php endif; ?>
          </td>
        </tr>
        <?php $i++; ?>
      <?php endforeach; ?>
    </tbody>
  </table>

  <br>
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

  <div>
    <div class="article-div">
      <strong><?php echo __('Total de alumnos'); ?>:</strong>
      <span class="little-box">
        <?php if ($examination_subject->getIsClosed()): ?>
          <?php echo $examination_subject->countTotalStudents(); ?>
        <?php endif; ?>
      </span>

      <strong><?php echo __('Aprobados'); ?>:</strong>
      <span class="little-box">
        <?php if ($examination_subject->getIsClosed()): ?>
          <?php echo $examination_subject->countApprovedStudents(); ?>
        <?php endif; ?>
      </span>

      <strong><?php echo __('Aplazados'); ?>:</strong>
      <span class="little-box">
        <?php if ($examination_subject->getIsClosed()): ?>
          <?php echo $examination_subject->countDisapprovedStudents(); ?>
        <?php endif; ?>
      </span>

      <strong><?php echo __('Ausentes'); ?>:</strong>
      <span class="little-box">
        <?php if ($examination_subject->getIsClosed()): ?>
          <?php echo $examination_subject->countAbsenceStudents(); ?>
        <?php endif; ?>
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

</div>
<div style="page-break-before: always;"></div>