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
<?php if (count($career_subjects)): ?>
  
  <?php $count_optionals = !is_null($school_year) ? array_sum(array_map(create_function('$o', 'return max(0, $o->countOptionalCareerSubjectsRelatedByCareerSubjectSchoolYearId() - 1);'), $career_subjects)) : 0 ?>
  
  <?php $style ="study_plan_". ($year & 1 ? "odd" : "even") ?>
  
  <tr class="<?php echo $style ?>">
    
    <td width="10%" rowspan="<?php echo count($career_subjects) + $count_optionals ?>">
      <?php echo $year ?>
    </td>
    
    <?php $career_subject = array_shift($career_subjects) ?>
    
    <?php if (!is_null($school_year)): ?>
      <?php $subject = $career_subject->getCareerSubject()->getSubject() ?>
      <?php $options = $career_subject->getOptionalCareerSubjectsRelatedByCareerSubjectSchoolYearId() ?>
    <?php else: ?>
      <?php $subject = $career_subject->getSubject() ?>
      <?php $options = array() ?>
    <?php endif ?>

    <?php $options_count = count($options) ?>

    <?php $rowspan = ($options_count > 0) ? ' rowspan="'.$options_count.'"' : "" ?>
    
    <td  width="30%" <?php $options_count > 0 and print ' rowspan="'.$options_count.'"' ?>>
      <?php echo $subject ?>
    </td>
    
    <td width="30%">
      <?php echo ($options_count > 0) ? array_shift($options)->getCareerSubjectSchoolYearRelatedByChoiceCareerSubjectSchoolYearId()->getCareerSubject()->getSubject() : "-" ?>
    </td>
    
    <td width="30%" <?php echo $rowspan ?>  style="display: none" class="correlative">
      <?php $correlative_career_subjects = !is_null($school_year) ? $career_subject->getCareerSubject()->getCorrelativeCareerSubjects() : $career_subject->getCorrelativeCareerSubjects() ?>
      
      <?php echo empty($correlative_career_subjects) ? '-' : implode(', ',array_map(create_function('$o','return $o->getSubject();'), $correlative_career_subjects)) ?>
    </td>
  </tr>
  
  <?php include_component('career', 'options', array('options' => $options , 'style' => $style)) ?>

  <?php include_component('career', 'careerSubjects', array('career_subjects' => $career_subjects , 'style' => $style, "school_year" => $school_year)) ?>

<?php endif ?>