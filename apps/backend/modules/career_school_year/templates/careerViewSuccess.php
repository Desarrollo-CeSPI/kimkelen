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
<?php use_helper('I18N') ?>
<?php use_helper('Javascript') ?>
<?php use_javascript('study_plan.js')?>
<?php include_partial("$module/assets") ?>

<div id="sf_admin_container">
  <div>
    <h1><?php echo __("Plan de estudio de ").$career->getCareerName().' ('.$career->getPlanName().')' ?></h1>
  </div>
  <ul class="sf_admin_actions">
    <li class="sf_admin_action_list">
      <?php echo link_to(__('Volver al listado de carreras', array(), 'messages'), '@career' , array()) ?>
    </li>
    <li class="sf_admin_action_correlatives">
      <?php echo link_to_function('Ver correlativas', "toggleCorrelatives('.correlative');") ?>
    </li>
  </ul>
  <?php include_partial('career/study_plan', array('career'=> $career, "school_year" => $school_year))?>

  <ul class="sf_admin_actions">
    <li class="sf_admin_action_list">
      <?php echo link_to(__('Volver al listado de carreras', array(), 'messages'), "@$module", array()) ?>
    </li>
    <li class="sf_admin_action_print">
      <?php //echo link_to(__('Imprimir plan de estudios', array(), 'messages'), '@print_career_view?career_id='.$career->getId() , array()) ?>
    </li>
  </ul>
</div>