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
<?php include_partial('optional_school_year/assets') ?>
<?php include_javascripts_for_form($form) ?>

<div id="sf_admin_container">
  <h1><?php echo __('Opciones de %career_subject_school_year%', array('%career_subject_school_year%' => $career_subject_school_year->__toString())) ?></h1>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('optional_school_year/updateOptional') ?>" method="post">
      <ul class="sf_admin_actions">
        <?php echo $helper->linkToList(array(  'label' => 'Volver al listado de materias',  'params' =>   array(  ),  'class_suffix' => 'list',)) ?>
        <?php echo $helper->linkToSaveAndList($form->getObject(), array(  'params' =>   array(  ),  'class_suffix' => 'save_and_list',  'label' => 'Save and list',)) ?>
      </ul>
      
      <input type="hidden" name="id" value="<?php echo $career_subject_school_year->getId() ?>" />
      <?php echo $form->renderHiddenFields() ?>      

      <fieldset>

        <h2><?php echo __('Opciones') ?></h2>
        <?php echo $form?>

      </fieldset>
      
      <ul class="sf_admin_actions">
        <?php echo $helper->linkToList(array(  'label' => 'Volver al listado de materias',  'params' =>   array(  ),  'class_suffix' => 'list',)) ?>
        <?php echo $helper->linkToSaveAndList($form->getObject(), array(  'params' =>   array(  ),  'class_suffix' => 'save_and_list',  'label' => 'Save and list',)) ?>
      </ul>
    </form>
  </div>
</div>