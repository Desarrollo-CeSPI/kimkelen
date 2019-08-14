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
<?php use_helper('I18N', 'Form', 'Javascript') ?>

<div id="login">
  <div class="left">
    <div class="logo"></div>
  </div>

  <form action="<?php echo url_for('@sf_guard_signin') ?>" method="post">
    <?php echo $form->renderHiddenFields() ?>
    <div class="right">
      <div class="title_login">Login</div>

      <div class="form_row">

        <?php echo $form ?>
      </div>
      <div class="form_row">
        <input type="submit" value="Ingresar" name="commit" class="button"/>
      </div>
    </div>
  </form>
  <div class="form_footer">
    <div class="logo_footer">


      © 2009 - <?php echo date('Y') ?>| CeSPI - UNLP | <?php echo __('versión %%number%%', array('%%number%%' => sfConfig::get('app_version_number', 1))) ?>
       <?php echo image_tag("logo-kimkelen-footer-white.png", array('alt' => __('Kimkelen'))); ?>
    </div>
  </div>
</div><!--end login-->

<?php use_javascript('jquery.js') ?>
<?php javascript_tag() ?>
jQuery('#signin_username').focus();
<?php end_javascript_tag() ?>