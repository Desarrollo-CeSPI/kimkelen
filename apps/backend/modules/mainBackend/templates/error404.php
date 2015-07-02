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
<?php use_stylesheet('errors') ?>

<div class="error-container">
  <div class="error">
    <h1><?php echo __('Disculpe') ?></h1>

    <h2><?php echo __('Ocurrió un error al procesar su pedido') ?></h2>

    <p>
      <?php echo __('Esto se puede haber producido por varias razones, entre ellas:') ?>
    </p>

    <ul>
      <li><?php echo __('La dirección fue mal tipeada') ?></li>
      <li><?php echo __('Ocurrió una situación anómala en la aplicación mientras se procesaba la información') ?></li>
    </ul>

    <p>
      <span class="emphasized"><?php echo __('Por favor intente nuevamente en unos instantes.') ?></span>
      <?php echo __('Si el error persiste, comuníquese con los administradores del sistema.') ?>
    </p>
  </div>

  <div class="error-footer">
    <?php echo link_to(__('Volver al inicio'), '@homepage', array('class' => 'go-home')) ?>
  </div>
</div>