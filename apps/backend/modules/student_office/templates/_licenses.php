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
<?php if (!isset($person)):?>
  <?php $person = $personal->getPerson();?>
<?php endif?>

<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_username">
  <div>
    <label for="username"> <?php echo __('Licenses');?> </label>
    <?php if ($person->countLicenses()):?>
      <table>
        <thead>
          <tr>
            <td><?php echo __('License type')?></td>
            <td><?php echo __('Date from')?></td>
            <td><?php echo __('Date to')?></td>
            <td><?php echo __('Is active')?></td>
          </tr>
        </thead>
        <tbody>
          <?php foreach($person->getLicenses() as $license):?>
            <tr>
              <td><?php echo $license->getLicenseType()?></td>
              <td><?php echo $license->getDateFrom()?></td>
              <td><?php echo $license->getDateTo()?></td>
              <td><?php include_partial('personal/list_field_boolean', array('value' => $license->getIsActive()))?></td>
            </tr>
          <?php endforeach?>
       </tbody>
      </table>
    <?php else:?>
      <?php echo __('Dont have any license')?>
    <?php endif?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>