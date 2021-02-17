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
<?php include_partial('course_student_mark/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Calificaciones del curso %course%', array('%course%' => strval($course))) ?></h1>

  <?php include_partial('course_student_mark/flashes') ?>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('course_student_mark/update') ?>" method="post">
      <input type="hidden" id="id" name="id" value="<?php echo $course->getId() ?>"/>
      <ul class="sf_admin_actions">
        <li class="sf_admin_action_list"><?php echo link_to(__('Back'), "@$referer_module") ?></li>
        <?php if (!$course->getIsClosed()):?>
          <li class="sf_admin_action_save"><input type="submit" value="<?php echo __('Guardar cambios') ?>" /></li>
        <?php endif?>
      </ul>

      <div>

        <?php if (!$course->getIsClosed()):?>
          <?php echo __('Cargar notas para la materia:') ?>
        <?php else: ?>
          <?php echo __('Notas de la materia:') ?>
        <?php endif?>
        <?php $first = true ?>
        <?php foreach ($course_subjects as $course_subject): ?>
          <?php if ($course_subject->getCareerSubject()->getHasOptions()): ?>
            <?php continue ?>
          <?php endif; ?>

          <a class="tab<?php $first and print ' tab-selected' ?>" href="#marks_fieldset_<?php echo $course_subject->getId() ?>" onclick="jQuery('fieldset').hide(); jQuery(jQuery(this).attr('href')).show(); jQuery('.tab').removeClass('tab-selected'); jQuery(this).addClass('tab-selected'); return false;"><?php echo $course_subject->getCareerSubject()->toStringWithCareer() ?></a>
          <?php $first = false ?>
        <?php endforeach; ?>
      </div>

      <?php foreach ($course_subjects as $course_subject): ?>
        <?php if ($course_subject->getCareerSubject()->getHasOptions()): ?>
          <?php continue ?>
        <?php endif; ?>

        <fieldset id="marks_fieldset_<?php echo $course_subject->getId() ?>" class="marks-fieldset">
          <h2><?php echo $course_subject->getCareerSubject() ?></h2>
          <?php include_component('course_student_mark', 'marks_not_averageable', array('course' => $course, 'course_subject' => $course_subject, 'form' => $forms[$course_subject->getId()])) ?>
        </fieldset>
      <?php endforeach; ?>

      <ul class="sf_admin_actions">
        <li class="sf_admin_action_list"><?php echo link_to(__('Back'), "@$referer_module") ?></li>
        <?php if (!$course->getIsClosed()):?>
          <li class="sf_admin_action_save"><input type="submit" value="<?php echo __('Guardar cambios') ?>" /></li>
        <?php endif?>
      </ul>
    </form>
  </div>
</div>

<script type="text/javascript">
  jQuery('fieldset:gt(0)').hide();
</script>