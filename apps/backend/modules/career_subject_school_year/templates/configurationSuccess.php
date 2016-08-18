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
<?php use_helper('Javascript', 'Object','I18N','Form') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php get_javascripts_for_form($form)?>

<div id="sf_admin_container">
  <h1><?php echo __('Configuración de la materia  %career_subject_school_year%', array('%career_subject_school_year%' => $career_subject_school_year->__toString())) ?></h1>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('career_subject_school_year/updateConfiguration') ?>" method="post">
      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Volver al listado'), '@career_subject_school_year', array('class' => 'sf_admin_action_go_back')) ?></li>
        <li><input type="submit" value="<?php echo __('Guardar') ?>" /></li>
      </ul>
      <input type="hidden" name="id" value="<?php echo $career_subject_school_year->getId() ?>" />
      <?php echo $form->renderHiddenFields() ?>

      <fieldset>

        <h2><?php echo __('Subject configuration') ?></h2>
        <?php # echo $form?>
        <?php echo strtr($form->getWidgetSchema()->getFormFormatter()->getDecoratorFormat(), array("%content%" => (string) $form)) ?>

      </fieldset>

      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Volver al listado'), '@career_subject_school_year', array('class' => 'sf_admin_action_go_back')) ?></li>
        <li><input type="submit" value="<?php echo __('Guardar') ?>" /></li>
      </ul>
    </form>
  </div>
     <div style="margin-top: 1px; clear: both;">
     </div>

  </div>

</div>

<?php echo javascript_tag("
  jQuery(document).ready(function() {
    if(jQuery('#subject_configuration_promotion_method').is(':checked'))
      jQuery('#subject_configuration_promotion_mark').removeAttr('disabled');
  });
")?>

<?php echo javascript_tag("
  jQuery(document).ready(function() {
    if(jQuery('#subject_configuration_promotion_method').is(':checked'))
      jQuery('#subject_configuration_promotion_mark').removeAttr('disabled');
  });
")?>