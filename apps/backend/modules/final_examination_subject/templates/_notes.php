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
<?php foreach ($form->getObject()->getFinalExaminationSubjectStudents() as $fess):?>
  <?php $mark = 'student_'. $fess->getId() . '_mark'?>
  <?php $is_absent = 'student_'. $fess->getId() . '_is_absent'?>

  <div class="sf_admin_form_row">
    <?php echo $form[$mark]->renderError() ?>
    <?php echo $form[$mark]->renderLabel() ?><?php echo $form[$mark] ?>
    <?php if (isset($form[$is_absent])): ?>
      <?php echo $form[$is_absent]->render()?><span style="margin-left: 10px"><?php echo __("Is absent") ?>?</span>
    <?php endif ?>
    <div class="help">
      <?php echo $form[$mark]->renderHelp() ?>
    </div>
  </div>
<?php endforeach?>