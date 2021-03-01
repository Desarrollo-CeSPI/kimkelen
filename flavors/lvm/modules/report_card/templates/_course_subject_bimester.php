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

<?php $is_repproved = $student_career_school_year->isRepproved() ?>
<div class="title"><?php echo __('Marks') ?></div>
<table class="gridtable">
  <?php if (count($course_subject_students_first_q = $student->getCourseSubjectStudentsForBimesterFirstQuaterly($student_career_school_year)) > 0): ?>
    <tr>
      <?php include_partial('th_bimester_tabular', array('division' => $division, 'number' => '1', 'course_subject_students' => $course_subject_students_first_q)) ?>
    </tr>
  <?php endif; ?>
  <?php $count_marks = 0; ?>
  <?php foreach ($course_subject_students_first_q as $course_subject_student): ?>
    <?php $count_marks = ($course_subject_student->getCourseSubject()->countMarks() > $count_marks) ? $course_subject_student->getCourseSubject()->countMarks() : $count_marks ?>
  <?php endforeach; ?>

  <?php foreach ($course_subject_students_first_q as $course_subject_student): ?>
    <tr>
      <td class='subject_name'><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getName() ?></td>
      <?php for ($i = 1; $i <= $count_marks; $i++): ?>
        <td><?php echo $course_subject_student->getMarkForIsClosed($i) ?></td>
      <?php endfor; ?>

      <?php $course_result = $course_subject_student->getCourseResult() ?>
      
      <td><?php echo ($course_result && $course_subject_student->getAllMarksWithNote()) ? $course_result->getResultStr() : '' ?></td>
      
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(1)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>


      <?php if (!is_null($student_repproved_course_subject = $course_subject_student->repprovedCourseSubjectHasBeenApproved())): ?>
        <td><?php echo ($student_repproved_course_subject->getLastMarkStr()) ?></td>
      <?php else: ?>
        <td></td>
      <?php endif; ?>

      <td>
        <?php if ($is_repproved): ?>
          <?php echo $course_subject_student->getFinalAvg() ?>
        <?php else: ?>
          <?php if( !is_null($course_result) && $course_result->getCourseSubjectStudent()->getIsNotAverageable() && ! is_null($course_result->getCourseSubjectStudent()->getNotAverageableCalification())): ?>
            <?php if($course_result->getCourseSubjectStudent()->getNotAverageableCalification() == NotAverageableCalificationType::APPROVED): ?>
              <?php echo __("Trayectoria completa"); ?>
            <?php else: ?>  
                <?php echo __("Trayectoria en curso"); ?>
            <?php endif; ?>
          
          <?php else: ?> 
          <?php echo $student->getPromDef($course_result) ?>
          <?php endif ?>
        <?php endif ?>
      </td>

      <?php if (!$division->hasAttendanceForDay()): ?>
        <?php foreach ($periods[0] as $period): ?>
          <td>
            <?php
            echo $period->getIsClosed() ? round($student->getTotalAbsences($division->getCareerSchoolYearId(), $period, $course_subject_student->getCourseSubjectId(), true), 2) : '&nbsp'
            ?>
          </td>
        <?php endforeach; ?>
      <?php endif; ?>

    </tr>
  <?php endforeach; ?>
    
  
  <?php $css_one_q= $student->getCourseSubjectStudentsForFirstQuaterly( $student_career_school_year)?>
  <?php if(count($css_one_q) > 0):?>
  <?php $count_marks = 0; ?>
  <?php foreach ($css_one_q as $course_subject_student): ?>
    <?php $count_marks = ($course_subject_student->getCourseSubject()->countMarks() > $count_marks) ? $course_subject_student->getCourseSubject()->countMarks() : $count_marks ?>
  <?php endforeach; ?>
    <tr>
        <th class='th-subject-name'><?php echo __('') ?></th>
        <?php $max_marks = 0 ?>
        <?php foreach ($css_one_q as $course_subject_student): ?>
          <?php $max_marks = ($course_subject_student->getCourseSubject()->countMarks() > $max_marks) ? $course_subject_student->getCourseSubject()->countMarks() : $max_marks ?>
        <?php endforeach; ?>


        <?php for ($mark_number = 1; $mark_number <= $max_marks; $mark_number++): ?>
          <th><?php echo __($mark_number . '°C') ?></th>
        <?php endfor; ?>

        <th><?php echo __('Prom.') ?></th>
        <th><?php echo __('Ex.R.') ?></th>
        <th><?php echo __('Ex.C.') ?></th>
        <th><?php echo __('Ex.P.') ?></th>
        <th><?php echo __('Prom.Def.') ?></th>
        <th><?php echo __('Inasist. 1°C') ?></th>
    </tr>
    
    
    <?php foreach ($css_one_q as $course_subject_student): ?>
    <tr>
      <td class='subject_name'><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getName() ?></td>
      <?php for ($i = 1; $i <= $count_marks; $i++): ?>
        <td><?php echo $course_subject_student->getMarkForIsClosed($i) ?></td>
      <?php endfor; ?>

      <?php $course_result = $course_subject_student->getCourseResult() ?>
      
      <td><?php echo ($course_result && $course_subject_student->getAllMarksWithNote()) ? $course_result->getResultStr() : '' ?></td>
      
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(1)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>


      <?php if (!is_null($student_repproved_course_subject = $course_subject_student->repprovedCourseSubjectHasBeenApproved())): ?>
        <td><?php echo ($student_repproved_course_subject->getLastMarkStr()) ?></td>
      <?php else: ?>
        <td></td>
      <?php endif; ?>

      <td>
        <?php if ($is_repproved): ?>
          <?php echo $course_subject_student->getFinalAvg() ?>
        <?php else: ?>
          <?php if( !is_null($course_result) && $course_result->getCourseSubjectStudent()->getIsNotAverageable() && ! is_null($course_result->getCourseSubjectStudent()->getNotAverageableCalification())): ?>
            <?php if($course_result->getCourseSubjectStudent()->getNotAverageableCalification() == NotAverageableCalificationType::APPROVED): ?>
              <?php echo __("Trayectoria completa"); ?>
            <?php else: ?>  
                <?php echo __("Trayectoria en curso"); ?>
            <?php endif; ?>
          
          <?php else: ?> 
          <?php echo $student->getPromDef($course_result) ?>
          <?php endif ?>
        <?php endif ?>
      </td>

      <?php if (!$division->hasAttendanceForDay()): ?>
      <?php $first_quaterly = CareerSchoolYearPeriodPeer::retrieveFirstQuaterlyForCareerSchoolYear($division->getCareerSchoolYear()); ?>
        
          <td>
            <?php
            echo $first_quaterly->getIsClosed() ? round($student->getTotalAbsences($division->getCareerSchoolYearId(), $first_quaterly, $course_subject_student->getCourseSubjectId(), true), 2) : '&nbsp'
            ?>
          </td> 
      <?php endif; ?>

    </tr>
  <?php endforeach; ?>
  <?php endif;?>  
</table>


<table class="gridtable">
  <?php if (count($course_subject_students_second_q = $student->getCourseSubjectStudentsForBimesterSecondQuaterly($student_career_school_year)) > 0): ?>
    <tr>
      <?php include_partial('th_bimester_tabular', array('division' => $division, 'number' => '2', 'course_subject_students' => $course_subject_students_second_q)) ?>
    </tr>
  <?php endif; ?>
  <?php $first = true ?>
  <?php $historia = SchoolBehaviourFactory::getEvaluatorInstance()->getHistoriaDelArteForSchoolYear($student_career_school_year->getCareerSchoolYear()->getSchoolYear()) ?>
  <?php $count_marks = 0; ?>
  <?php foreach ($course_subject_students_second_q as $course_subject_student): ?>
    <?php $count_marks = ($course_subject_student->getCourseSubject()->countMarks() > $count_marks) ? $course_subject_student->getCourseSubject()->countMarks() : $count_marks ?>
  <?php endforeach; ?>

  <?php foreach ($course_subject_students_second_q as $course_subject_student): ?>
    <?php $career_subject_school_year = $course_subject_student->getCourseSubject()->getCareerSubjectSchoolYear() ?>

    <?php $is_historia = $historia->getId() == $career_subject_school_year->getId() || in_array($career_subject_school_year->getCareerSubject()->getId(), array(261, 262)); ?>
    <tr>
      <td class="subject_name"><?php echo $name = $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getName() ?></td>

      <?php for ($i = 1; $i <= $count_marks; $i++): ?>
        <td><?php echo $course_subject_student->getMarkForIsClosed($i) ?></td>
      <?php endfor; ?>

      <td><?php echo ($course_result = $course_subject_student->getCourseResult()) ? $course_result->getResultStr() : '' ?></td>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(1)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>

      <?php if (!is_null($student_repproved_course_subject = $course_subject_student->repprovedCourseSubjectHasBeenApproved())): ?>
        <td><?php echo ($student_repproved_course_subject->getLastMarkStr()) ?></td>
      <?php else: ?>
        <td><?php #echo $course_subject_student->getLastStudentDisapprovedCourseSubject()  ?></td>
      <?php endif; ?>

      <td>
        <?php if ($is_repproved): ?>
          <?php echo $course_subject_student->getFinalAvg() ?>
        <?php else: ?>
          <?php if( !is_null($course_result) && $course_result->getCourseSubjectStudent()->getIsNotAverageable() && ! is_null($course_result->getCourseSubjectStudent()->getNotAverageableCalification())): ?>
            <?php if($course_result->getCourseSubjectStudent()->getNotAverageableCalification() == NotAverageableCalificationType::APPROVED): ?>
              <?php echo __("Trayectoria completa"); ?>
            <?php else: ?>  
                <?php echo __("Trayectoria en curso"); ?>
            <?php endif; ?>
          
          <?php else: ?> 
          <?php echo $student->getPromDef($course_result) ?>
          <?php endif ?>
        <?php endif ?>
      </td>

      <?php if (!$division->hasAttendanceForDay()): ?>
        <?php foreach ($periods[1] as $period): ?>
          <td>
            <?php
            echo $period->getIsClosed() ? round($student->getTotalAbsences($division->getCareerSchoolYearId(), $period, $course_subject_student->getCourseSubjectId(), true), 2) : '&nbsp'
            ?>
          </td>
        <?php endforeach; ?>
      <?php endif; ?>

    </tr>
  <?php endforeach; ?>
    
    
  <?php $css_two_q= $student->getCourseSubjectStudentsForSecondQuaterly( $student_career_school_year)?>
  <?php if(count($css_two_q) > 0):?>
  <?php $count_marks = 0; ?>
  <?php foreach ($css_two_q as $course_subject_student): ?>
    <?php $count_marks = ($course_subject_student->getCourseSubject()->countMarks() > $count_marks) ? $course_subject_student->getCourseSubject()->countMarks() : $count_marks ?>
  <?php endforeach; ?>
    <tr>
        <th class='th-subject-name'><?php echo __('') ?></th>
        <?php $max_marks = 0 ?>
        <?php foreach ($css_two_q as $course_subject_student): ?>
          <?php $max_marks = ($course_subject_student->getCourseSubject()->countMarks() > $max_marks) ? $course_subject_student->getCourseSubject()->countMarks() : $max_marks ?>
        <?php endforeach; ?>
       
        <th><?php echo __('2°C') ?></th>
        <th><?php echo __('Prom.') ?></th>
        <th><?php echo __('Ex.R.') ?></th>
        <th><?php echo __('Ex.C.') ?></th>
        <th><?php echo __('Ex.P.') ?></th>
        <th><?php echo __('Prom.Def.') ?></th>
        <th><?php echo __('Inasist. 2°C') ?></th>
    </tr>
    
    
    <?php foreach ($css_two_q as $course_subject_student): ?>
    <tr>
      <td class='subject_name'><?php echo $course_subject_student->getCourseSubject()->getCareerSubject()->getSubject()->getName() ?></td>
      <?php for ($i = 1; $i <= $count_marks; $i++): ?>
        <td><?php echo $course_subject_student->getMarkForIsClosed($i) ?></td>
      <?php endfor; ?>

      <?php $course_result = $course_subject_student->getCourseResult() ?>
      
      <td><?php echo ($course_result) ? $course_result->getResultStr() : '' ?></td>
      
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(1)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>
      <td><?php echo (($course_result instanceOf StudentDisapprovedCourseSubject) && $course_subject_student_examination = $course_subject_student->getCourseSubjectStudentExaminationsForExaminationNumber(2)) ? $course_subject_student_examination->getMarkStr() : '' ?></td>


      <?php if (!is_null($student_repproved_course_subject = $course_subject_student->repprovedCourseSubjectHasBeenApproved())): ?>
        <td><?php echo ($student_repproved_course_subject->getLastMarkStr()) ?></td>
      <?php else: ?>
        <td></td>
      <?php endif; ?>

      <td>
        <?php if ($is_repproved): ?>
          <?php echo $course_subject_student->getFinalAvg() ?>
        <?php else: ?>
          <?php echo $student->getPromDef($course_result) ?>
        <?php endif ?>
      </td>

      <?php if (!$division->hasAttendanceForDay()): ?>
      <?php $second_quaterly = CareerSchoolYearPeriodPeer::retrieveSecondQuaterlyForCareerSchoolYear($division->getCareerSchoolYear()); ?>
        
          <td>
            <?php
            echo $second_quaterly->getIsClosed() ? round($student->getTotalAbsences($division->getCareerSchoolYearId(), $second_quaterly, $course_subject_student->getCourseSubjectId(), true), 2) : '&nbsp'
            ?>
          </td> 
      <?php endif; ?>

    </tr>
  <?php endforeach; ?> 
    
   <?php endif;?> 
</table>
