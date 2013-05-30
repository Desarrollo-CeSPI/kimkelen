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
<h3>Division: <?php echo $division =  $pager->getParameter('division')?></h3>
<?php if($sf_user->hasCredential('edit_course')):?>

  <ul>
    <?php if ($division->canCopyStudentsToCourses()):?>
      <li><?php echo link_to(__('Add division students to all courses'), 'division_course/copyStudentsToCourses')?></li>
    <?php endif?>
    <?php $unrelated_career_subjects = $division->getUnrelatedCareerSubjects()?>
    <?php if (count($unrelated_career_subjects)): ?>
      <div class="info-box">
        <div class="info-box-title">
          <strong><?php echo link_to_function(__("Courses that are not created for the division"), "jQuery('#not_related_courses').toggle();") ?></strong>
        </div>
        <strong><?php echo link_to( __("Crear todas las materias para la division que faltan"), 'division_course/createAllCourses?id=' . $division->getId());?></strong>
        <div class="info-box-collapsable" id="not_related_courses" >
          <?php foreach ($unrelated_career_subjects as $career_subject):?>
            <li><?php echo link_to( __("Create course for: %%career%%", array('%%career%%' => $career_subject)), 'division_course/createCourse?id='. $career_subject->getId())?></li>
          <?php endforeach?>
        </div>
      </div>
    <?php endif ?>
  </ul>
<?php endif?>

<ul class="sf_admin_actions">
  <li><?php echo link_to(__('Back to divisions'), '@division', array('class' => 'sf_admin_action_go_back')) ?></li>
</ul>