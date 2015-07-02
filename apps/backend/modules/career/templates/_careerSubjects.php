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
<?php foreach($career_subjects as $career_subject): ?>
  
  <?php $options = !is_null($school_year) ? $career_subject->getOptionalCareerSubjectsRelatedByCareerSubjectSchoolYearId() : array() ?>
  <?php $options_count = count($options) ?>
  
  <tr class=<?php echo $style ?>>
    
    <?php $rowspan = ($options_count > 0) ? ' rowspan="'.$options_count.'"' : "" ?>
    
    <td width="30%"  <?php echo $rowspan ?>>
      <?php echo !is_null($school_year) ? $career_subject->getCareerSubject()->getSubject() : $career_subject->getSubject() ?>
    </td>
    <?php if ($options_count > 0): ?>
      <td width="30%">
        <?php echo array_shift($options)->getCareerSubjectSchoolYearRelatedByChoiceCareerSubjectSchoolYearId()->getCareerSubject()->getSubject() ?>
      </td>
    <?php else:?>
      <td width="30%" >-</td>
    <?php endif?>
    <td width="30%" <?php echo $rowspan ?>  style="display: none" class="correlative">
      <?php $correlative_career_subjects = !is_null($school_year) ? $career_subject->getCareerSubject()->getCorrelativeCareerSubjects() : $career_subject->getCorrelativeCareerSubjects() ?>
      
      <?php echo empty($correlative_career_subjects) ? '-' : implode(', ', array_map(create_function('$o','return $o->getSubject();'), $correlative_career_subjects)) ?>
    </td>
  </tr>
  <?php include_component('career', 'options', array('options' => $options, 'style' => $style)) ?>

<?php endforeach?>