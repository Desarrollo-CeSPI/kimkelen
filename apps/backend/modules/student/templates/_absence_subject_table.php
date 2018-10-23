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
<?php $attendances_array = array(); ?>
<?php foreach ($student->getStudentAttendancesAbsencePerSubject($student_career_school_year->getCareerSchoolYear()) as $student_attendances): ?>
    <?php if (!isset($attendances_array[$student_attendances->getCourseSubjectId()])): ?>
        <?php $attendances_array[$student_attendances->getCourseSubjectId()] = array(); ?>
    <?php endif ?>
    <?php $attendances_array[$student_attendances->getCourseSubjectId()][] = $student_attendances; ?>
<?php endforeach; ?>
<!--fin armado de arreglo   $attendances_array  -->

<?php include_partial('student/information_box') ?>

<?php $is_for_period = $student_career_school_year->isAbsenceForPeriod()?>
<?php foreach ($attendances_array as $student_attendances): ?>
    <?php $course_subject = $student_attendances[0]->getCourseSubject() ?>
    <div class="course_subject_absence_show">
        <?php echo ($course_subject->getCourse()->getIsPathway()) ? $course_subject .' ('. __('Pathway') .')' : $course_subject; ?>
        <a class="absence_subject_a" href="#" onclick="jQuery('#absence_subject_table_<?php echo $course_subject->getId() ?>').toggle()"><?php echo __('show details') ?> </a>
        <?php $configurations = $course_subject->getCourseSubjectConfigurations() ?>

        <div id ="absence_subject_table_<?php echo $course_subject->getId() ?>"hidden="hidden" >
            <table style="width: 100%">
                <thead>
                    <tr>
                        <?php if ($is_for_period):?>
                          <th>Periodo</th>
                        <?php endif?>

                        <th><?php echo __('Day') ?></th>
                        <th><?php echo __('Absence') ?></th>
                        <th><?php echo __('Justification') ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php $career_school_year = $student_career_school_year->getCareerSchoolYear()?>

                    <?php if ($is_for_period):?>

                        <?php foreach ($configurations as $config):?>

                            <?php $period = $config->getCareerSchoolYearPeriod() ?>    
                            <?php $free_class = $student->getFreeClass($period, $course_subject, $career_school_year) ?>

                            <tr class ="<?php echo $free_class ?>">

                                <?php $absences = $student->getAbsences($student_career_school_year->getCareerSchoolYearId(), $period, $course_subject->getId()); ?>

                                <td ROWSPAN="<?php echo count($absences) == 0 ? '' : count($absences); ?>">
                                    <?php echo $period ?>
                                </td>

                                <?php if (count($absences) == 0): ?>
                                    <td></td>
                                    <td><?php echo __('The student dont have any absence in this period') ?></td>
                                    <td></td>
                                <?php endif ?>

                                <?php foreach ($absences as $absence): ?>
                                    <td class="<?php !is_null($absence->getStudentAttendanceJustificationId()) and print "box_justificated_studet_show" ?>">
                                        <?php echo $absence->getFormattedDay() ?>
                                    </td>
                                    <td class="<?php !is_null($absence->getStudentAttendanceJustificationId()) and print "box_justificated_studet_show" ?>">
                                        <?php echo $absence->getValue() ?>
                                    </td>
                                    <td class="<?php !is_null($absence->getStudentAttendanceJustificationId()) and print "box_justificated_studet_show" ?>">
                                        <?php echo is_null($absence->getStudentAttendanceJustificationId()) ? '-' : $absence->getStudentAttendanceJustification()->getJustificationType() ?>
                                    </td>
                                </tr>
                                <tr class ="<?php $is_free and print "box_free" ?> <?php $is_almost_free and print "box_almost_free" ?>">
                                <?php endforeach ?>
                                </tr>
                                    <tr class ="<?php $is_free and print "box_free" ?> <?php $is_almost_free and print "box_almost_free" ?>">
                                    <?php $total_with_justification     = round($student->getTotalAbsences($student_career_school_year->getCareerSchoolYearId(), $period, $course_subject->getId(), true), 2 )?>
                                    <?php $total_with_out_justification = round($student->getTotalAbsences($student_career_school_year->getCareerSchoolYearId(), $period, $course_subject->getId(), false), 2) ?>
                                    <td colspan="2" >
                                      <?php echo __('Subtotal (with justificated): %subtotal%', array(
                                          '%subtotal%' => $total_with_justification))
                                      ?>
                                    </td>
                                    <td colspan="2" >
                                      <?php echo __('Subtotal  (without justificated): %subtotal%', array(
                                          '%subtotal%' => $total_with_out_justification))
                                      ?>
                                    </td>
                                </tr>
                          </tr>
                        <?php endforeach ?>
                        <?php if (count($configurations) == 0 && $course_subject->getCourse()->getIsPathway()):?>
                            <?php $absences = $student->getAbsences($student_career_school_year->getCareerSchoolYearId(), NULL, $course_subject->getId()); ?>
                            <?php if (count($absences) == 0): ?>
                                    <td>-</td>
                                    <td><?php echo __('The student dont have any absence in this period') ?></td>
                                    <td></td>
                                    <td></td>
                            <?php else:?>
                                <?php foreach ($absences as $absence): ?>
                                <tr>
                                    <td>-</td>
                                    <td>
                                        <?php echo $absence->getFormattedDay() ?>
                                    </td>
                                    <td>
                                        <?php echo $absence->getValue() ?>
                                    </td>
                                    <td>
                                        <?php echo is_null($absence->getStudentAttendanceJustificationId()) ? '-' : $absence->getStudentAttendanceJustification()->getJustificationType() ?>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                                 <tr class ="<?php $is_free and print "box_free" ?> <?php $is_almost_free and print "box_almost_free" ?>">
                                    <?php $total_with_justification     = round($student->getTotalAbsences($student_career_school_year->getCareerSchoolYearId(), NULL, $course_subject->getId(), true), 2 )?>
                                    <?php $total_with_out_justification = round($student->getTotalAbsences($student_career_school_year->getCareerSchoolYearId(), NULL, $course_subject->getId(), false), 2) ?>
                                    <td colspan="2" >
                                      <?php echo __('Subtotal (with justificated): %subtotal%', array(
                                          '%subtotal%' => $total_with_justification))
                                      ?>
                                    </td>
                                    <td colspan="2" >
                                      <?php echo __('Subtotal  (without justificated): %subtotal%', array(
                                          '%subtotal%' => $total_with_out_justification))
                                      ?>
                                    </td>
                                </tr>   
                            <?php endif ?>
                          
                        <?php endif;?>

                    <?php else:?>
                        <?php $free_class = $student->getFreeClass(null, $course_subject, $career_school_year) ?>

                            <tr class ="<?php echo $free_class ?>">
                                <?php $absences = $student->getAbsences($student_career_school_year->getCareerSchoolYearId(), null, $course_subject->getId()) ;?>


                                <?php if (count($absences) == 0): ?>
                                    <td></td>
                                    <td><?php echo __('The student dont have any absence in this period') ?></td>
                                    <td></td>
                                <?php endif ?>

                                <?php foreach ($absences as $absence): ?>
                                    <td class="<?php !is_null($absence->getStudentAttendanceJustificationId()) and print "box_justificated_studet_show" ?>">
                                        <?php echo $absence->getFormattedDay() ?>
                                    </td>
                                    <td class="<?php !is_null($absence->getStudentAttendanceJustificationId()) and print "box_justificated_studet_show" ?>">
                                        <?php echo $absence->getValue() ?>
                                    </td>
                                    <td class="<?php !is_null($absence->getStudentAttendanceJustificationId()) and print "box_justificated_studet_show" ?>">
                                        <?php echo is_null($absence->getStudentAttendanceJustificationId()) ? '-' : $absence->getStudentAttendanceJustification() ?>
                                    </td>
                                </tr>
                                <tr class ="<?php echo $free_class ?>">
                                <?php endforeach ?>
                                </tr>
                                    <tr class ="<?php echo $free_class ?>">
                                    <?php $total_with_justification     = round($student->getTotalAbsences($student_career_school_year->getCareerSchoolYearId(), null, $course_subject->getId(), true), 2) ?>
                                    <?php $total_with_out_justification = round($student->getTotalAbsences($student_career_school_year->getCareerSchoolYearId(), null, $course_subject->getId(), false), 2) ?>

                                    <td colspan="2" >
                                      <?php echo __('Subtotal (with justificated): %subtotal%', array(
                                          '%subtotal%' => $total_with_justification))
                                      ?>
                                    </td>
                                    <td colspan="2" >
                                      <?php echo __('Subtotal  (without justificated): %subtotal%', array(
                                          '%subtotal%' => $total_with_out_justification))
                                      ?>
                                    </td>
                                </tr>
                          </tr>
                    <?php endif?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach ?>