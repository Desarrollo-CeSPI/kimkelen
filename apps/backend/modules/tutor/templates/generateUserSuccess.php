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
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div id="sf_admin_container">
  <h1><?php echo __('Generate user for tutor %tutor%', array("%tutor%" => $tutor->getPerson()->getFullName())) ?></h1>
  <div>
	<div class="warning change_status">
	    <?php echo __('Se generará una contraseña automática y será enviada a la cuenta de email registrada en el sistema.') ?>
	</div>
  </div>
	
  <div id="sf_admin_content">
    <form action="<?php echo url_for('tutor/generateUser') ?>" method="post">

      <input type="hidden" name="tutor_id" value="<?php echo $tutor->getId() ?>" />
      <fieldset>
           <?php echo strtr($form->getWidgetSchema()->getFormFormatter()->getDecoratorFormat(), array("%content%" => (string) $form)) ?>
        
      </fieldset>
      
      <ul class="sf_admin_actions">
          <li><?php echo link_to(__('Back'), "@tutor", array('class' => 'sf_admin_action_go_back')) ?></li>
          <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
      </ul>
    </form>
  </div>
</div>