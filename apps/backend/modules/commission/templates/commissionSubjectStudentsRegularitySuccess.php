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
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css', 'first') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css', 'first') ?>

<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<div id="sf_admin_container">
  <h1>
    <?php echo __('Manage Students Regularity for commission %%course_name%%', array('%%course_name%%' => $course)) ?>
  </h1>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('commission/commissionSubjectStudentsRegularity') ?>" method="post">
      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), '@commission', array('class' => 'sf_admin_action_go_back')) ?></li>
        <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
      </ul>
      <input type="hidden" value="<?php echo $course->getId()?>" name="id"/>
      <?php if(count($course->getCourseSubjectStudents()) > 0): ?>
        <table>
          <thead>
            <tr>
              <th><?php echo __('Students'); ?></th>
              <th><?php echo __('Absences'); ?></th>
              <th><?php echo __('Free?'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($course->getCourseSubjectStudents() as $course_subject_student): ?>
            <tr>
              <td>
                <?php echo $form['student_'.$course_subject_student->getId()]->render();?>
              </td>
              <td></td>
              <td style="text-align:center;">
                <?php echo $form['free_student_'.$course_subject_student->getId()]->render();?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <?php echo __('The commission has no students.'); ?>
      <?php endif; ?>
      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Back'), '@commission', array('class' => 'sf_admin_action_go_back')) ?></li>
        <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
      </ul>
    </form>
  </div>
  <div style="margin-top: 1px; clear: both;">
  </div>
</div>