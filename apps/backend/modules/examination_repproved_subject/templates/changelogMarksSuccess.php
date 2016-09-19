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
  <h1><?php echo __('Auditoria de notas para la mesa de examen "%examination%"', array('%examination%' => $examination_repproved_subject->getExamination())) ?></h1>
    
  <ul class="sf_admin_actions">
    <li><?php echo link_to(__('Back'), '@examination_repproved_subject', array('class' => 'sf_admin_action_go_back')) ?></li>
  </ul>
  
  <h2><?php echo __('Materia: %subject%', array('%subject%' => $examination_repproved_subject->getSubject())) ?></h2>
  <table style='width: 100%'>
    <thead>
      <tr>
        <th align='center'><?php echo __('Nombre y apellido'); ?></th>
        <th align='center'><?php echo __('Notas') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($students as $student): ?>
      <tr>
        <td style='text-align: left; width: 30%'>
          <?php echo $student ?>
        </td>
        <td>   
          <?php $ers = $examination_repproved_subject->getExaminationNoteForStudent($student) ?>
          <span>
            <?php echo __('Nota: %mark%', array('%mark%' => !$ess->getMark()? ($ess->getIsAbsent()? __('Absent') : '-') : $ess->getMark()));?>
            <?php echo ncChangelogRenderer::render($ers, 'tooltip', array('credentials' => 'view_changelog')); ?>
          </span>
          </br>
        </td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
  
  <ul class="sf_admin_actions">
    <li><?php echo link_to(__('Back'), '@examination_repproved_subject', array('class' => 'sf_admin_action_go_back')) ?></li>
  </ul>
</div>