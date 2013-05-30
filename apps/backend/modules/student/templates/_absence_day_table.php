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
            <?php if ($student_career_school_year->isAbsenceForPeriod()):?>
                <th><?php echo __('Period') ?></th>
            <?php endif?>
            <th><?php echo __('Absence') ?></th>
            <th><?php echo __('Justification') ?></th>
            <th><?php echo __('Absence value') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0 ?>
        <?php $career_school_year = $student_career_school_year->getCareerSchoolYear()?>

        <?php if ($student_career_school_year->isAbsenceForPeriod()):?>

            <?php foreach ($periods as $period): ?>
                <?php $free_class = $student->getFreeClass($period, null, $career_school_year) ?>

                <tr class ="<?php echo $free_class ?>">
                    <?php $absences = $student->getAbsences($student_career_school_year->getCareerSchoolYearId(), $period) ?>

                    <td ROWSPAN="<?php echo count($absences) == 0 ? '' : count($absences); ?>">
                        <div><?php echo $period->getName() ?></div>
                        <div><?php echo $period->getTermStr() ?></div>
                    </td>

                    <?php if (count($absences) == 0): ?>
                        <td><?php echo __('The student dont have any absence in this period') ?></td>
                        <td></td>
                        <td>0.0</td>
                    <?php endif ?>

                    <?php foreach ($absences as $absence): ?>
                        <td><?php echo $absence->getFormattedDay() ?></td>
                        <td><?php echo is_null($absence->getStudentAttendanceJustificationId()) ? '-' : $absence->getStudentAttendanceJustification() ?></td>
                        <td><?php echo $absence->getValue() ?></td>
                        </tr>
                        <tr class ="<?php $is_free and print "box_free" ?> <?php $is_almost_free and print "box_almost_free" ?>">
                    <?php endforeach ?>

                    <?php $remaining = $student->getRemainingAbsenceFor($period, null, true, $career_school_year) ?>
                    <?php $subtotal = round($student->getTotalAbsences($student_career_school_year->getCareerSchoolYearId(), $period, null, true), 2) ?>
                    <tr class="<?php $is_free and print "box_free" ?> <?php $is_almost_free and print "box_almost_free" ?> total_absence">
                        <td colspan="2" ><?php echo __('Remaining absence (with justificated): %remaining%', array('%remaining%' => $remaining)) ?></td>
                        <td colspan="2" ><?php echo __('Subtotal (with justificated): %subtotal%', array('%subtotal%' => $subtotal)) ?></td>
                    </tr>
                    <?php $remaining = $student->getRemainingAbsenceFor($period, null, false, $career_school_year) ?>
                    <?php $subtotal = round($student->getTotalAbsences($student_career_school_year->getCareerSchoolYearId(), $period, null, false), 2) ?>
                    <tr class="<?php $is_free and print "box_free" ?> <?php $is_almost_free and print "box_almost_free" ?> total_absence">
                        <td colspan="2" ><?php echo __('Remaining absence (without justificated): %remaining%', array('%remaining%' => $remaining)) ?></td>
                        <td colspan="2" ><?php echo __('Subtotal  (without justificated): %subtotal%', array('%subtotal%' => $subtotal)) ?></td>
                    </tr>

            <?php endforeach ?>
            <tr class="total_absence">
                <td colspan="3"  class="total_absence" ><?php echo __('Total') ?></td>
                <td class="total_absence"><?php echo round($student->getTotalAbsences($student_career_school_year->getCareerSchoolYearId(), null, null, false), 2) ?></td>
            </tr>
        <?php else:?>
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
                            <td><?php echo is_null($absence->getStudentAttendanceJustificationId()) ? '-' : $absence->getStudentAttendanceJustification() ?></td>
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
        <?php endif?>

    </tbody>
</table>