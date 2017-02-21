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

<header class="header-login text-center">
    <?php echo image_tag("frontend/kimkelen_logo.png", array('alt' => __('Kimkelen'))); ?>
</header>

<div class="container">
  <div class="form-login">
    <form class="" action="<?php echo url_for('@sf_guard_signin') ?>" method="post">
      <?php echo $form ?>
      <button class="btn btn-block" type="submit"> <?php echo __('Sign in') ?></button>
    </form>
  </div>
</div>

<footer>
    <div class="logo_footer">
        <?php echo link_to(image_tag("logo-kimkelen-footer.png", array('alt' => __('Kimkelen'))), '@homepage', array('title' => __('Inicio'))) ?>
    </div>
    © <?php echo date('Y') ?>| CeSPI - UNLP | <?php echo __('v%%number%%', array('%%number%%' => sfConfig::get('app_version_number', 1))) ?>
</footer>
