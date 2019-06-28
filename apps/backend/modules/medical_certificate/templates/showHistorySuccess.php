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
<?php use_helper('I18N', 'Date', 'Javascript') ?>
<?php include_partial('medical_certificate/assets') ?>

<div id="sf_admin_container">
    <h1>  <?php echo __("History") .' - '. $medical_certificate->getDescription() ?> </h1>
    <ul class="sf_admin_actions">
        <li class ="sf_admin_action_list"><?php echo link_to(__('Back'), $back_url); ?></li>    
    </ul>
    <div id="sf_admin_content">
	<table style="width: 100%">
            <thead>
              <tr>
                <th><?php echo __('Last date update') ?></th>
                <th><?php echo __('User') ?></th>
                <th><?php echo __('Description') ?></th>
                <th><?php echo __('Certificate status') ?></th>
                <th><?php echo __('Date') ?></th>
                <th><?php echo __('Theoric class') ?></th>
                <th><?php echo __('Theoric class from') ?></th>
                <th><?php echo __('Theoric class to') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($logs as $l):?>
                <tr>
                    <td><?php echo $l->getUpdatedAt()?></td>
                    <td><?php echo $l->getUsername() ?></td>
                    <td><?php echo $l->getDescription() ?></td>
                    <td><?php echo BaseCustomOptionsHolder::getInstance('MedicalCertificateStatus')->getStringFor($l->getCertificateStatusId()) ?></td>
                    <td><?php echo format_date($l->getDate(), 'd/MM/yyyy') ?></td>
                    <td><?php echo ($l->getTheoricClass()) ? 'Sí': 'No' ?></td>
                    <td><?php echo (!is_null($l->getTheoricClassFrom())) ? format_date($l->getTheoricClassFrom(), 'd/MM/yyyy'):'' ?></td>
                    <td><?php echo (!is_null($l->getTheoricClassTo())) ? format_date($l->getTheoricClassTo(), 'd/MM/yyyy'):'' ?></td>
                </tr>
              <?php endforeach;?>
            </tbody>
         </table>

		<ul class="sf_admin_actions">
                    <li class ="sf_admin_action_list"><?php echo link_to(__('Back'), $back_url); ?></li>
		</ul>
    </div>
 </div>	