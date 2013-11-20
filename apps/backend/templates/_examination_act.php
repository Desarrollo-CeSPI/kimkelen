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
    <a href="<?php echo url_for('manual_examination_subject') ?>"><?php echo __('Go back') ?></a>
    <a href="#" onclick="window.print(); return false;"><?php echo __('Print') ?></a>
</div>


<div class="record-wrapper">

    <div class="record-header">
        <div>
            <div class="logo"><?php echo image_tag("kimkelen_logo.png", array('absolute' => true)) ?></div>
        </div>
        <div>
            <?php echo __('Acta Volante de Exámenes'); ?>
        </div>
    </div>

    <div class="report-content">


        <div class="row">
            Fecha:.................... Hora: ..................
        </div>

        <div class="row">
            Exámenes de Alumnos:
            <strong>
            <?php if ($examination_subject->getExamination()->getExaminationNumber() == 1): ?>
                <?php echo 'Regulares'; ?>
            <? elseif ($examination_subject->getExamination()->getExaminationNumber() == 2): ?>
                <?php echo 'Febrero/Marzo'; ?>
            <?php else: echo 'Previas'; ?>
            <?php endif; ?>
            </strong>
        </div>


        <div class="row">
            Asignatura: <strong><?php echo $examination_subject->getSubject() ?></strong>
            Año:  <strong><?php echo $examination_subject->getExamination()->getSchoolYear() ?></strong>
        </div>


        <table class="gridtable">
            <thead>
            <tr class="printColumns">
                <th rowspan="2">N° de Orden</th>
                <th rowspan="2"><?php echo __('Apellido y Nombre'); ?></th>
                <th rowspan="2"><?php echo __('División'); ?></th>
                <th colspan="2"><?php echo __('Calificación'); ?></th>
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
                    <td><?php echo $i ?> </td>
                    <td><?php echo $student ?> </td>
                    <td><?php echo implode(', ', DivisionPeer::retrieveStudentSchoolYearDivisions($examination_subject->getCareerSchoolYear(), $student)); ?> </td>
                    <td>
                         <?php if($examination_subject->getIsClosed()):?>
                            <?php echo $examination_subject->getExaminationNoteForStudent($student)->getMark() ?>
                         <?php endif;?>
                    </td>
                    <td> <?php if($examination_subject->getIsClosed()):?>
                        <?php echo $examination_subject->getExaminationNoteForStudent($student)->getMarkText() ?>
                        <?php endif;?>
                    </td>
                </tr>
                <?php $i++; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    Art 34º: <span class="sub">Exámenes regulares:</span> La evaluación en las mesas de exámenes regulares, regulares complementarios o regulares previos, se realizará sobre aquellos contenidos desarrollados durante el ciclo lectivo cursado.
</div>

<div class="row">
    Art 35º: <span class="sub">Exámenes libres:</span> La evaluación en las mesas de exámenes de alumnos libres, se realizará sobre aquellos contenidos del programa del ciclo lectivo cursado de acuerdo a las reglamentaciones vigentes.
</div>


<div class="row">
    En ambos casos: "La evaluación podrá ser oral y/o escrita y/o práctica. Siempre que se utilice más de una modalidad, éstas no podrán ser eliminatorias entre sí, debiendo tener un carácter complementario.
</div>


<div class="row">
    <div class="observation-box">
        Observaciones
    </div>
</div>

<div class="row">
    La Mesa Examinadora para la evaluación de los alumnos inscriptos en la presente acta, ha utilizado la modalidad (marcar lo que corresponda)

    <table class="examination-type-boxes">
        <tr>
            <td>Oral:</td>
            <td class="boxed"></td>
            <td>Escrita:</td>
            <td class="boxed"></td>
            <td>Práctica</td>
            <td class="boxed"></td>
        </tr>
    </table>
</div>

<div class="row">
    <table class="examination-count-boxes">
        <tr>
            <td>Total de Alumnos:</td>
            <td class="boxed"><?php if($examination_subject->getIsClosed()):?>
                                 <?php echo $examination_subject->countTotalStudents();?>
                              <?php endif;?>
            </td>
        </tr>
        <tr>
            <td>Aprobados:</td>
            <td class="boxed"><?php if($examination_subject->getIsClosed()):?>
                                 <?php echo $examination_subject->countApprovedStudents();?>
                             <?php endif;?>
            </td>

        </tr>
        <tr>
            <td>Aplazados:</td>
            <td class="boxed"><?php if($examination_subject->getIsClosed()):?>
                <?php echo $examination_subject->countDisapprovedStudents();?>
                <?php endif;?>
            </td>
        </tr>
        <tr>
            <td>Ausentes:</td>
            <td class="boxed"><?php if($examination_subject->getIsClosed()):?>
                <?php echo $examination_subject->countAbsenceStudents();?>
                <?php endif;?>
            </td>
        </tr>
    </table>
</div>

<div class="row">
    La Plata, ........de .........................de .....
</div>

<div class="row">
    <div class="signature signature-center">
        <div class="signature-text">Presidente</div>
        <div class="signature-subtext">Firma y Aclaración</div>
    </div>
    <div class="signature signature-left">
        <div class="signature-text">Vocal</div>
        <div class="signature-subtext">Firma y Aclaración</div>
    </div>
    <div class="signature signature-right">
        <div class="signature-text">Vocal</div>
        <div class="signature-subtext">Firma y Aclaración</div>
    </div>
</div>

<div style="page-break-before: always;">

</div>


