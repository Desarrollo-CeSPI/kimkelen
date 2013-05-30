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
<?php use_helper('Javascript', 'Object','I18N','Form', 'Asset') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Close %final_examination_subject%', array('%final_examination_subject%' => $final_examination_subject->getSubject()))?></h1>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('final_examination_subject/realClose') ?>" method="post">
      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), '@final_examination_subject', array('class' => 'sf_admin_action_go_back')) ?></li>
        <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
      </ul>
      <input type="hidden" id="id" name="id" value="<?php echo $final_examination_subject->getId() ?>"/>

      <fieldset id="califications_fieldset">
        <table>
          <tr>
            <th><?php echo __("Student") ?></th>
            <th><?php echo __("Mark") ?></th>            
          </tr>
          <?php foreach ($final_examination_subject->getFinalExaminationSubjectStudents() as $fess): ?>
            <tr class="<?php echo $fess->getResultClass() ?>">
              <td><?php echo $fess->getStudent() ?></td>
              <td><?php echo $fess->getMark() ? $fess->getMark() : __("Is absent") ?></td>
            </tr>
          <?php endforeach ?>
        </table>
      </fieldset>

      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), '@final_examination_subject', array('class' => 'sf_admin_action_go_back')) ?></li>
        <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
      </ul>
    </form>
  </div>
  <div style="margin-top: 1px; clear: both;">
  </div>
</div>