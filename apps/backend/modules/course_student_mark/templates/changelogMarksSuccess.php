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
<?php include_partial('assets') ?>
 
<div id="sf_admin_container">
  <h1><?php echo __('Auditoria de notas para el curso "%course%"', array('%course%' => $course)) ?></h1>
    
  <ul class="sf_admin_actions">
    <li class="sf_admin_action_list">
      <?php echo link_to(__("Back"), url_for($previous_url)) ?>
    </li>
  </ul>
  <?php foreach ($course_subjects as $cs):
    $course = $cs->getCourse();
    $configuration = $cs->getCareerSubjectSchoolYear()->getConfiguration();
  ?>
    <h2><?php echo __('Materia: %course_subject%', array('%course_subject%' => $cs->getCareerSubjectSchoolYear())) ?></h2>
    <table style='width: 100%'>
      <thead>
        <tr>
          <th align='center'><?php echo __('Nombre y apellido'); ?></th>
          <th align='center'><?php echo __('Notas') ?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($cs->getCourseSubjectStudents() as $course_subject_student): ?>
        <tr>
          <td style='text-align: left; width: 30%'>
            <?php echo $course_subject_student->getStudent() ?>
          </td>
          <td>   
          <?php $i = 1; ?>     	
          <?php foreach ($course_subject_student->getCourseSubjectStudentMarks() as $key => $cssm): ?>   
            <?php if (!$cssm->getMark()): ?>
              <span><?php echo 'Nota '. $i .': - ' ?></span></br>
            <?php else: ?> 
              <span>
                <?php echo 'Nota '. $i .': ' . $cssm->getMarkByConfig($configuration) ?>
                <?php include_partial('show_change_log', array('mark' => $cssm)) ?>
              </span>
            </br>
            <?php endif ?>
            <?php $i++; ?>
          <?php endforeach ?>
          </td>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>
  <?php endforeach ?>

  <ul class="sf_admin_actions">
    <li class="sf_admin_action_list">
      <?php echo link_to(__("Back"), url_for($previous_url)) ?>
    </li>
  </ul>
</div>