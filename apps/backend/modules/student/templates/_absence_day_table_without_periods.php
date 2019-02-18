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
<?php include_partial('student_attendance/information_box') ?>
<table style="width: 100%">
    <thead>
        <tr>
            <th><?php echo __('Absence') ?></th>
            <th><?php echo __('Justification') ?></th>
            <th><?php echo __('Absence value') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0 ?>
        <?php $career_school_year = $student_career_school_year->getCareerSchoolYear()?>
        <?php $free_class = $student->getFreeClass(null, null, $career_school_year) ?>

                    <?php $absences = $student->getAbsences($student_career_school_year->getCareerSchoolYearId()) ?>

                    <?php if (count($absences) == 0): ?>
                        <tr>
                            <td><?php echo __('The student dont have any absence in this period') ?></td>
                            <td></td>
                            <td>0.0</td>
                        </tr>
                    <?php endif ?>

                    <?php foreach ($absences as $absence): ?>
                        <tr class ="<?php echo $free_class ?>">
                            <td><?php echo $absence->getFormattedDay() ?></td>
                            <td><?php echo is_null($absence->getStudentAttendanceJustificationId()) ? '-' : $absence->getStudentAttendanceJustification()->getJustificationType() ?></td>
                            <td><?php echo $absence->getValue() ?></td>
                        </tr>
                    <?php endforeach ?>

                    <?php $remaining = $student->getRemainingAbsenceFor(null, null, true, $career_school_year) ?>

                    <?php $subtotal = round($student->getTotalAbsences($student_career_school_year->getCareerSchoolYearId(), null, null, true), 2) ?>

                    <tr class ="<?php echo $free_class ?> total_absence">
                        <td colspan="2" ><?php echo __('Remaining absence (with justificated): %remaining%', array('%remaining%' => $remaining)) ?></td>
                        <td colspan="2" ><?php echo __('Subtotal (with justificated): %subtotal%', array('%subtotal%' => $subtotal)) ?></td>
                    </tr>
                    <?php $remaining = $student->getRemainingAbsenceFor(null, null, false, $career_school_year) ?>
                    <?php $subtotal = round($student->getTotalAbsences($student_career_school_year->getCareerSchoolYearId(),null, null, false), 2) ?>
                    <tr class ="<?php echo $free_class ?> total_absence">
                        <td colspan="2" ><?php echo __('Remaining absence (without justificated): %remaining%', array('%remaining%' => $remaining)) ?></td>
                        <td colspan="2" ><?php echo __('Subtotal  (without justificated): %subtotal%', array('%subtotal%' => $subtotal)) ?></td>
                    </tr>
                    <tr class="total_absence">
                        <td colspan="2"  class="total_absence" ><?php echo __('Total') ?></td>
                        <td class="total_absence"><?php echo round($student->getTotalAbsences($student_career_school_year->getCareerSchoolYearId(), null, null, false), 2) ?></td>
                    </tr>

    </tbody>
</table>