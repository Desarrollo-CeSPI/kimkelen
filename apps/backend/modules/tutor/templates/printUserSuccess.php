<?php /*
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
<?php use_helper('Javascript', 'Object', 'I18N', 'Asset') ?>
<?php use_stylesheet('/css/print-assistanceAndSanction.css', '', array('media' => 'print')) ?>
<?php use_stylesheet('/css/assistanceAndSanction.css') ?>

<div class="non-printable">
	<a href="#" onclick="window.print(); return false;"><?php echo __('Print') ?></a>
	<a href="<?php echo url_for('tutor') ?>"><?php echo __('Go back') ?></a>
</div>

<div class="report-wrapper">
	<div class="report-header">
		<div><?php echo image_tag("kimkelen_logo.png") ?></div>
	</div>

	<h2><?php echo __('Usuario de acceso al sistema Kimkëlen-Tutores') ?></h2>

	<h3><?php echo __("Username and password will allow you to log in the system."); ?><h3>
	<h4><?php echo __("You will be asked to change your password when you login for the first time."); ?></h4>
			
	<div class="colsbottom">
			<p><?php echo __('Username') ?>: <strong><?php echo $user->getUsername(); ?></strong></p>
			<p><?php echo __('Password') ?>: <strong><?php echo $password; ?></strong></p>
		  <p><?php echo __('Email') ?>: <strong><?php echo $tutor->getPerson()->getEmail(); ?></strong></p>
			<p><?php echo __("Enter system following this link"); ?> <strong>: <?php echo $link ?></strong></p>
	</div>
</div>