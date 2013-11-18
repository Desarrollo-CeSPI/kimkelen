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
<?php use_stylesheet('examination-record.css') ?>
<?php use_stylesheet('print-examination-record.css', 'last', array('media' => 'print')) ?>

<div class="non-printable">
  <a href="<?php echo url_for('examination_repproved_subject') ?>"><?php echo __('Go back') ?></a>
  <a href="#" onclick="window.print(); return false;"><?php echo __('Print') ?></a>
</div>
<div class="record-wrapper">

  <div class="record-header">
    <div>
      <div class="logo"><?php echo image_tag("kimkelen_logo.png", array('absolute' => true)) ?></div>
      <div class="logo_fvet"><?php echo image_tag("fvet.jpeg", array('absolute' => true)) ?></div>
      <div class="school-name">Escuela de Educación Técnico Profesional </div>
      <div class="school-name">de Nivel Medio en Producción Agropecuaria y Agroalimentaria</div>
      <div class="school-name">Facultad de Ciencias Veterinarias de la UBA</div>
    </div>
  </div>
  <div class="report-content">
    <h2>Acta de examen</h2>
    <div class="gray-background">
      <span><strong><?php echo 'Condición'; ?></strong>:
        <?php if ($examination_repproved_subject->getExaminationRepproved()->getExaminationType() == ExaminationRepprovedType::REPPROVED): ?>
          <?php echo 'Previa' ?>
        <?php else: echo 'Libre' ?>
        <?php endif; echo $examination_repproved_subject->getExaminationRepproved() ?>
      </span>
      <span class="right"><strong><?php echo __('School year'); ?></strong>: <?php echo $examination_repproved_subject->getExaminationRepproved()->getSchoolYear() ?></span>
    </div>
    <p>Acta de exámenes de la asignatura <strong><?php echo $examination_repproved_subject->getCareerSubject()->getSubject() ?></strong></p>
    <p>Examinados los alumnos que se mencionan a continuación, han merecido las calificaciones consignadas en la presente Acta,
      que firman los señores profesores <strong><?php echo $examination_repproved_subject->getTeachersToString(); ?></strong>.

    <table class="gridtable">
      <thead>
        <tr class="printColumns"></tr>
      <th>N°</th>
      <th><?php echo __('Student'); ?></th>
      <th><?php echo __('Folio number'); ?></th>
      <th><?php echo __('Identification number'); ?></th>
      <th><?php echo __('Division') ?></th>
      <th><?php echo __('Examination date') ?></th>
      <th><?php echo __('Calification') ?></th>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        <?php foreach ($students as $student): ?>
          <tr>
            <td><?php echo $i ?> </td>
            <td><?php echo $student ?> </td>
            <td><?php echo $student->getPerson()->getStudent()->getFolioNumber() ?> </td>
            <td><?php echo $student->getPerson()->getIdentificationNumber() ?> </td>
            <?php $division = DivisionStudentPeer::retrieveDivisionsForStudentAndYear($student, $examination_repproved_subject->getCareerSubject()->getYear()); ?>
            <td><?php echo implode(', ', $division); ?> </td>
            <?php $sers = $examination_repproved_subject->getExaminationNoteForStudent($student); ?>
             <td><?php echo $sers->getDate('d/m/Y'); ?> </td>
             <?php if ($sers->getIsAbsent()): ?>
              <td><?php echo __('Is absent') ?></td>
            <?php else: ?>
              <td> <?php echo $sers->getMark() ?> </td>
            <?php endif; ?>
          </tr>
          <?php $i++; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="signature signature-center">
  <div class="signature-text">Presidente</div>
</div>
<div class="signature signature-left">
  <div class="signature-text">Vocal</div>
</div>
<div class="signature signature-right">
  <div class="signature-text">Vocal</div>
</div>
<div style="page-break-before: always;"></div>