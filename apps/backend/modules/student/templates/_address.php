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
<?php if (!$sf_user->hasCredential('show_student_min')): ?>
  <h2><?php echo __('Address') ?> </h2>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_state">
    <div>
    <label for="state"> <?php echo ('Provincia');?> </label>
      <?php echo $student->getPerson()->getAddress()->getState();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_city">
    <div>
    <label for="city"> <?php echo ('Ciudad');?> </label>
      <?php echo $student->getPerson()->getAddress()->getCity();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_street">
    <div>
    <label for="street"> <?php echo ('Calle');?> </label>
      <?php echo $student->getPerson()->getAddress()->getStreet();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_number">
    <div>
    <label for="number"> <?php echo ('Número');?> </label>
      <?php echo $student->getPerson()->getAddress()->getNumber();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_floor">
    <div>
    <label for="floor"> <?php echo __('Piso');?> </label>
      <?php echo (is_null($student->getPerson()->getAddress()->getFloor()) | $student->getPerson()->getAddress()->getFloor() == '') ? 'No ha sido especificado' : $student->getPerson()->getAddress()->getFloor();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_flat">
    <div>
    <label for="flat"> <?php echo __('Departamento');?> </label>
      <?php echo (is_null($student->getPerson()->getAddress()->getFlat()) | $student->getPerson()->getAddress()->getFlat() == '') ? 'No ha sido especificado' : $student->getPerson()->getAddress()->getFlat();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
<?php endif ?>