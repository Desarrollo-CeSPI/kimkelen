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
<?php use_helper('I18N', 'Date','Object','Form');?>

<?php include_partial('career/assets') ?>

<div id="sf_admin_container">
  <div>
    <h1><?php echo __('Configuración de materias de la carrera: %%nombre_carrera%%', array("%%nombre_carrera%%" => $career->getCareerName()))?></h1>
  </div>
  
  <ul class="sf_admin_actions">
      <li class="sf_admin_action_list">
        <?php echo link_to(__('Volver al listado de carreras', array(), 'messages'), 'career/index' , array()) ?>
      </li>
  </ul>
  
  <div id="sf_admin_content">
    <fieldset id="sf_fieldset_career_subjects_configuration">
      <h2><?php echo __("Configuración", array(), 'messages') ?></h2>
      <div class="sf_admin_form_row sf_admin_text">
        <div>
          <?php echo $form["marks"]->renderLabel() ?>

          <?php echo $form["marks"]->getValue() ?>
        </div>
        <div style="margin-top: 1px; clear: both"></div>
      </div>
      
      <div class="sf_admin_form_row sf_admin_text">
        <div>
          <?php echo $form["free_method"]->renderLabel() ?>
          <?php include_partial('career/list_field_boolean', array('value' => $form["free_method"]->getValue())); ?>
        </div>
        <div style="margin-top: 1px; clear: both"></div>
      </div>
      
      <div class="sf_admin_form_row sf_admin_text">
        <div>
          <?php echo $form["regular_mark"]->renderLabel() ?>

          <?php echo $form["regular_mark"]->getValue() ?>
        </div>
        <div style="margin-top: 1px; clear: both"></div>
      </div>
      
      <div class="sf_admin_form_row sf_admin_text">
        <div>
          <?php echo $form["promotion_method"]->renderLabel() ?>
          <?php include_partial('career/list_field_boolean', array('value' => $form["promotion_method"]->getValue())); ?>
        </div>
        <div style="margin-top: 1px; clear: both"></div>
      </div>
      
      <?php if($form["promotion_method"]->getValue()): ?>
        <div class="sf_admin_form_row sf_admin_text">
          <div>
            <?php echo $form["promotion_mark"]->renderLabel() ?>

            <?php echo $form["promotion_mark"]->getValue() ?>
          </div>
          <div style="margin-top: 1px; clear: both"></div>
        </div>
      <?php endif; ?>
    </fieldset>
  </div>

  <ul class="sf_admin_actions">
      <li class="sf_admin_action_list">
        <?php echo link_to(__('Volver al listado de carreras', array(), 'messages'), '@career' , array()) ?>
      </li>
  </ul>
</div>