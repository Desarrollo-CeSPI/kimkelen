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
<?php use_helper('Javascript', 'Object', 'I18N', 'Asset') ?>
<?php use_stylesheet('/css/print-assistanceAndSanction.css', '', array('media' => 'print')) ?>
<?php use_stylesheet('/css/assistanceAndSanction.css') ?>

<div class="non-printable">
  <a href="#" onclick="window.print(); return false;"><?php echo __('Print') ?></a>
  <a href="<?php echo url_for('student') ?>"><?php echo __('Go back') ?></a>
</div>

<?php foreach ($student_career_school_years as $student_career_school_year):?>
  <?php $school_year = $student_career_school_year->getSchoolYear();?>
  <?php foreach ($student_career_school_year->getDivisions() as $division):?>
    <div class='print_page'>      
      <div style="margin: 0px 20px">
        <h3 class="print_school_year"><?php echo __('School year') . ": " . $school_year ?></h3>

        <h2 class="print_title" style="text-align:justify"><?php echo __('Student assistance and sanction report'); ?></h2>

        <?php if (count($student->getAbsences($student_career_school_year->getCareerSchoolYearId())) == 0): ?>
          <h3 style="text-align: left;"><?php echo __('No se registraron inasistencias para este alumno.'); ?></h3>
        <?php else: ?>
          <div class="print_body">
            <div style="padding-left: 10px; padding-right: 10px">
              <div style="float:left; margin-bottom: 10px ;font-size: 14px"><?php echo $student ?></div>
              <span style="float:right">
                <span style="font-size: 12px"><?php echo __('Year') . ": " . $student_career_school_year->getYear() . "°" ?></span>
                <span style="font-size: 12px"><?php echo __('Division') . ": " . $division->getDivisionTitle(); ?></span>
              </span>
            </div>
            <div style="clear:both"></div>
            <table class="print_table" style="text-align: center;">
              <thead>
                <tr class="printColumns">
                  <th><?php echo __('Day') ?></th>
                  <th><?php echo __('Absence') ?></th>
                  <th><?php echo __('Valor de la falta') ?></th>
                  <th><?php echo __('Descripción de la falta') ?></th>
                  <th><?php echo __('Subject') ?></th>
                  <th><?php echo __('Is justified') ?></th>
                  <th><?php echo __('Justification type id') ?></th>
                  <th><?php echo __('Description') ?></th>
                </tr>
              </thead>
              <tbody class="print_body">
                <?php foreach ($student->getAbsences($student_career_school_year->getCareerSchoolYearId()) as $absence): ?>
                  <tr>
                    <td><?php echo $absence->getFormattedDay(); ?></td>
                    <td><?php echo $absence->getValueString() ?></td>
                    <td><?php echo $absence->getValue() ?></td>
                    <td><?php echo $absence->getAbsenceType()->getDescription() ?></td>
                    <td><?php echo ($course_subject = $absence->getCourseSubject()) ? $absence->getCourseSubject() : '-' ?></td>
                    <td><?php echo ($justification = $absence->getStudentAttendanceJustification()) ? 'Sí' : 'No' ?></td>
                    <td><?php echo ($type = $absence->getStudentAttendanceJustification()) ? $absence->getStudentAttendanceJustification()->getJustificationType() : '-' ?></td>
                    <td><?php echo ($justification = $absence->getStudentAttendanceJustification()) ? $absence->getStudentAttendanceJustification()->getObservation() : '-' ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="6" style="padding:5px; text-align: left; border-bottom:1px solid #ccc; background:#f2f2f2">
                    <?php echo __('Total unjustified') . ': ' 
                        . round($student->getTotalAbsences($division->getCareerSchoolYearId(), null), 2) 
                    ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="6" style="padding:5px; text-align: left; border-bottom:1px solid #ccc; background:#f2f2f2">
                    <?php echo __('Total justified') . ': ' 
                        . round($student->getTotalJustificatedAbsences($division->getCareerSchoolYearId(), null, null), 2)
                    ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="6" style="padding:5px; text-align: left; border-bottom:1px solid #ccc; background:#f2f2f2">
                    <?php echo __('Total ') . ': ' 
                        . round($student->getTotalAbsences($division->getCareerSchoolYearId(), null, null, false), 2)
                    ?>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        <?php endif; ?>
        <?php if ($student->countStudentDisciplinarySanctionsForSchoolYear($school_year) == 0): ?>
          <h3 class="print_title" style="text-align: left;"><?php echo __('No se registraron sanciones para este alumno.'); ?></h3>
        <?php else: ?>
        <h3 class="print_title" style="text-align: left;"><?php echo __('Disciplinary sanctions'); ?></h3>
          <ul class="print_sanctions" style="text-align: left;">
            <?php foreach ($student->getStudentDisciplinarySanctionsForSchoolYear($school_year) as $ds): ?>
              <li>
                <span><?php echo $ds->getFormattedRequestDate() ?> - </span>
                <span><?php echo $ds->getValueString(); ?> - </span>
                <span><?php echo __('Resolution date') . ': ' . $ds->getFormattedResolutionDate(); ?> - </span>
                <span><?php echo __('Description') . ": " . $ds->getDisciplinarySanctionType(); ?> </span>
              </li>
            <?php endforeach; ?>
            <?php echo __('Total').":".$student->countStudentDisciplinarySanctionsForSchoolYear($school_year); ?>
          </ul>
        <?php endif; ?>
        <div class="colsbottom">
          <div class='cols'>
            <div class="rowfirm_responsible">
            </div>
            <div class="titletable" style="text-align:justify">Firma padre, madre o tutor</div>
          </div>
          <div class='cols'>
            <div class="rowfirm_authority">            
            </div>
            <div class="titletable" style="text-align:justify">Firma De la Autoridad</div>
          </div>
        </div>
        <h3 class="print_date" > <?php echo __('Issue date') ?>: <?php echo date('d/m/Y') ?> </h3>
      </div>
    </div>
  <?php endforeach?>
<?php endforeach?>