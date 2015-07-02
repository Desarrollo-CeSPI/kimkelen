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
<?php if ($sf_user->hasCredential('show_student') && !$sf_user->hasGroup('Profesor')): ?>
<div class="box student-box">
  <h2 class="toggler"><?php echo __('Alumnos') ?></h2>

  <div class="content hide-me">
    <?php //include_partial('mainBackend/objects_shortcuts', array('shortcuts' => $shortcuts, 'route' => 'student_show')) ?>

    <ul class="items-list">
      <li class="item">
        <?php //echo link_to(__('Listado completo de alumnos'), '@student_pre_index', array('class' => 'action list_action')) ?>
      </li>
      <?php if ($sf_user->hasCredential('edit_student')): ?>
        <li class="item">
          <?php //echo link_to(__('Crear nuevo alumno'), '@student_new', array('class' => 'action new_action')) ?>
        </li>
      <?php endif ?>
    </ul>
    <div class="break">
    </div>
  </div>
</div>
<?php endif ?>