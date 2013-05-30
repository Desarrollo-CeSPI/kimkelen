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
<fieldset id="sf_fieldset_datos_personales">
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_full_name">
    <div>
    <label for="full_name"> <?php echo ('Nombre completo');?> </label>
      <?php echo $personal->getPerson()->getFullName();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_identification_type_and_number">
    <div>
    <label for="identification_type_and_number"> <?php echo __('Tipo y número de documento');?> </label>
      <?php echo BaseCustomOptionsHolder::getInstance('IdentificationType')->getStringFor($personal->getPerson()->getIdentificationType()).': '.$personal->getPerson()->getIdentificationNumber();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_sex_type">
    <div>
    <label for="sex_type"> <?php echo __('Sexo');?> </label>
      <?php echo BaseCustomOptionsHolder::getInstance('SexType')->getStringFor($personal->getPerson()->getSex());?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_cuil">
    <div>
    <label for="cuil"> <?php echo __('Cuil');?> </label>
      <?php echo (is_null($personal->getPerson()->getCuil()) | $personal->getPerson()->getCuil() == '') ? 'No ha sido cargado' : $personal->getPerson()->getCuil();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_email">
    <div>
    <label for="email"> <?php echo __('Email');?> </label>
      <?php echo (is_null($personal->getPerson()->getEmail()) | $personal->getPerson()->getEmail() == '') ? 'No ha sido cargado' : $personal->getPerson()->getEmail();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_telefono">
    <div>
    <label for="telefono"> <?php echo __('Teléfono');?> </label>
      <?php echo (is_null($personal->getPerson()->getPhone()) | $personal->getPerson()->getPhone() == '') ? 'No ha sido cargado' : $personal->getPerson()->getPhone();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_birth_date">
    <div>
    <label for="birth_date"> <?php echo __('Fecha de nacimiento');?> </label>
      <?php echo $personal->getPerson()->getBirthdate('d-m-Y');?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_birth_country">
    <div>
    <label for="birth_country"> <?php echo __('País de nacimiento');?> </label>
      <?php echo (is_null($personal->getPerson()->getBirthCountry()) | $personal->getPerson()->getBirthCountry() == '') ? 'No ha sido cargado' : $personal->getPerson()->getBirthCountryRepresentation();?>    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_birth_state">
    <div>
    <label for="birth_state"> <?php echo __('Provincia de nacimiento');?> </label>
      <?php echo (is_null($personal->getPerson()->getBirthState()) | $personal->getPerson()->getBirthState() == '') ? 'No ha sido cargado' : $personal->getPerson()->getBirthStateRepresentation();?>    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_birth_city">
    <div>
    <label for="birth_city"> <?php echo __('Ciudad de nacimiento');?> </label>
      <?php echo (is_null($personal->getPerson()->getBirthCity()) | $personal->getPerson()->getBirthCity() == '') ? 'No ha sido cargada' : $personal->getPerson()->getBirthCityRepresentation();?>    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>

  <?php  include_partial('personal/person_photo', array('object' => $personal)); ?>

  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_observations">
    <div>
    <label for="observations"> <?php echo __('Observaciones');?> </label>
      <?php echo (is_null($personal->getPerson()->getObservations()) | $personal->getPerson()->getObservations() == '') ? 'No han sido cargadas' : $personal->getPerson()->getBirthCity();?>    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_username">
    <div>
    <label for="username"> <?php echo __('Nombre de usuario');?> </label>
      <?php echo (is_null($personal->getPerson()->getsfGuardUser())) ? 'No posee usuario' : $personal->getPerson()->getsfGuardUser()->getUsername();?>    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
</fieldset>