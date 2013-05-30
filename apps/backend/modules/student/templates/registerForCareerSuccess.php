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
<?php include_partial('student/assets') ?>
<?php include_javascripts_for_form($form) ?>

<div id="sf_admin_container">
  <h1><?php echo __('Career register')?></h1>

  <div id="sf_admin_content">
    <h2><?php echo __("Careers %student% is registered", array("%student%"=>$student))?></h2>
    <?php if ($student->countCareerStudents()): ?>
      <table style="width: 100%">
        <tr>
          <th><?php echo __("Career") ?></th>
          <th><?php echo __("Actions") ?></th>
        </tr>
      <?php foreach($student->getCareerStudents() as $cs ): ?>
        <tr>
          <td><?php echo $cs ?></td>
          <td>
            <ul class="sf_admin_actions">
              <li class="sf_admin_action_delete">

                <?php echo $sf_user->hasCredential('edit_student') && $cs->canBeDeleted()?
                  link_to(__('Delete'),'student/deleteRegistrationForCareer?career_student_id='.$cs->getId(),array('confirm'=>__('Are you sure?'))):'' ?>
              </li>
              <li class="sf_admin_action_history">
                <?php echo link_to(__("History"), "student/history?career_student_id=".$cs->getId()) ?>
              </li>
            </ul>
          </td>
        </tr>
      <?php endforeach ?>
      </table>
    <?php endif ?>
    <h2><?php echo __("Register to a new career", array("%student%"=>$student))?></h2>
    <form action="<?php echo url_for('student/updateRegistrationForCareer') ?>" method="post">
      <input type="hidden" name="id" value="<?php echo $student->getId() ?>" />
      <fieldset>

          <?php echo $form?>
      </fieldset>

      <ul class="sf_admin_actions">
        <?php echo $helper->linkToList(array(  'label' => __('Go back'),  'params' =>   array(  ),  'class_suffix' => 'list',)) ?>
        <?php echo $helper->linkToSave($form->getObject(), array(  'params' =>   array(  ),  'class_suffix' => 'save_and_list',  'label' => __('Save'),)) ?>
      </ul>
    </form>
  </div>
</div>