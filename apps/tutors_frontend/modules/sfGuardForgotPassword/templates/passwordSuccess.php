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
<header class="header-login text-center">
  <?php echo image_tag("frontend/kimkelen_logo.png", array('alt' => __('Kimkelen'))); ?>
</header>

<div class="container">
  <div class="form-login">
    <p><?php echo __("Your e-mail address will be asked in order to send you further instructions about how to reset your password.") ?></p>
  <form action="<?php echo url_for('@sf_guard_password') ?>" method="post">
    <table>
      <?php echo $form ?>
    </table>
    <br>
    <p><?php echo __("If you still do not have a registered user, you should ask for it at school."); ?></p>

    <button class="btn btn-block" type="submit"> <?php echo __('Change password') ?></button>
  </form>
    <br><?php echo link_to(__('Go back'), '@homepage'); ?>
  </div>
</div>

<footer>
  <div class="logo_footer">
    <?php echo link_to(image_tag("logo-kimkelen-footer.png", array('alt' => __('Kimkelen'))), '@homepage', array('title' => __('Inicio'))) ?>
  </div>
  © <?php echo date('Y') ?>| CeSPI - UNLP | <?php echo __('v%%number%%', array('%%number%%' => sfConfig::get('app_version_number', 1))) ?>
</footer>
