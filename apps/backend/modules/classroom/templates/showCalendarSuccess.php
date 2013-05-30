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
<?php use_helper('WeekCalendar') ?>
<?php include_partial('classroom/assets') ?>
<div id="sf_admin_container">
  <h1><?php echo __('Classroom %name% calendar',array('%name%'=>$classroom->getName())) ?></h1>
  <div id="sf_admin_content">
  <div class="sf_admin_form">
        <ul class="sf_admin_actions">
    <?php echo $helper->linkToList(array(  'label' => 'Volver al listado de aulas',  'params' =>   array(  ),  'class_suffix' => 'list',)) ?>
    </ul>
  </div>
  </div>
</div>
<?php echo WeekCalendar('calendar',$events); ?>