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
<?php use_helper("Form", "I18N") ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>

<div id="sf_admin_container">
  <h1 style="margin:0 0 20px;padding:2px;"><?php echo __('Cambiar contraseña')?></h1>

  <div id="sf_admin_content">
    <?php include_partial('flash')?>
    
   <div class="sf_admin_form">
    <form action="<?php echo url_for('sfGuardUser/changePassword') ?>" method="POST" <?php print 'enctype="multipart/form-data"' ?>>
          <ul class="sf_admin_actions">
              <li class="sf_admin_action_list"><?php echo link_to(__("Ir al inicio"), "@homepage")?></li>
              <li><input type="submit" value="<?php echo __('Guardar nueva contraseña') ?>"/></li>
          </ul>

            <?php echo $form->renderHiddenFields()?>
            <?php echo $form->renderGlobalErrors()?>
            <fieldset id="sf_fieldset_change_password">
              <h2><?php echo __("Establecer una nueva contraseña de acceso");?> </h2>

              <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_actual_password">
                <div>
                  <?php echo $form['actual_password']->renderError() ?>
                  <label class="required" for="changepassword_actual_password"><?php echo __("Contraseña actual");?></label>
                  <?php echo $form['actual_password'] ?>
                </div>
                <div style="margin-top: 1px; clear: both;"></div>
              </div>

              <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_password">
                <div>
                  <?php echo $form['password']->renderError() ?>
                  <label class="required" for="changepassword_password"><?php echo __("Nueva contraseña");?></label>
                  <?php echo $form['password'] ?>
                  <?php echo $form['password']->renderHelp() ?>
                </div>
                <div style="margin-top: 1px; clear: both;"></div>
              </div>

              <div class="sf_admin_form_row sf_admin_text sf_admin_form_field_confirm_password">
                <div>
                  <?php echo $form['confirm_password']->renderError() ?>
                  <label class="required" for="changepassword_confirm_password"><?php echo __("Repetir nueva contraseña");?></label>
                  <?php echo $form['confirm_password'] ?>
                </div>
                <div style="margin-top: 1px; clear: both;"></div>
              </div>
            </fieldset>

          <ul class="sf_admin_actions">
              <li class="sf_admin_action_list"><?php echo link_to(__("Ir al inicio"), "@homepage")?></li>
              <li><input type="submit" value="<?php echo __('Guardar nueva contraseña') ?>"/></li>
          </ul>
    </form>
    </div>
  </div>
</div>