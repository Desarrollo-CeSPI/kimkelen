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
      <th><?php echo __('Identification number'); ?></th>
      <th><?php echo __('Division') ?></th>
      <th><?php echo __('Calification') ?></th>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        <?php foreach ($students as $student): ?>
          <tr>
            <td><?php echo $i ?> </td>
            <td><?php echo $student ?> </td>
            <td><?php echo $student->getPerson()->getIdentificationNumber() ?> </td>
            <td><?php echo implode(', ', DivisionPeer::retrieveStudentSchoolYearDivisions($examination_repproved_subject->getCareerSubject()->getCareerSchoolYear()->getSchoolYear(), $student)); ?> </td>
            <?php $ers = $examination_repproved_subject->getExaminationNoteForStudent($student); ?>
             <?php if ($ers->getIsAbsent()): ?>
              <td><?php echo __('Is absent') ?></td>
            <?php else: ?>
              <td> <?php echo $ers->getMark() ?> </td>
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