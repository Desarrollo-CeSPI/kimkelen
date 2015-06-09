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
      <?php echo $tutor->getPerson()->getFullName();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_identification_type_and_number">
    <div>
    <label for="identification_type_and_number"> <?php echo __('Tipo y número de documento');?> </label>
      <?php echo BaseCustomOptionsHolder::getInstance('IdentificationType')->getStringFor($tutor->getPerson()->getIdentificationType()).': '.$tutor->getPerson()->getIdentificationNumber();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_sex_type">
    <div>
    <label for="sex_type"> <?php echo __('Sexo');?> </label>
      <?php echo BaseCustomOptionsHolder::getInstance('SexType')->getStringFor($tutor->getPerson()->getSex());?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_cuil">
    <div>
    <label for="cuil"> <?php echo __('CUIL');?> </label>
      <?php echo (is_null($tutor->getPerson()->getCuil()) | $tutor->getPerson()->getCuil() == '') ? 'No ha sido cargado' : $tutor->getPerson()->getCuil();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_email">
    <div>
    <label for="email"> <?php echo __('Email');?> </label>
      <?php echo (is_null($tutor->getPerson()->getEmail()) | $tutor->getPerson()->getEmail() == '') ? 'No ha sido cargado' : $tutor->getPerson()->getEmail();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_telefono">
    <div>
    <label for="telefono"> <?php echo __('Teléfono');?> </label>
      <?php echo (is_null($tutor->getPerson()->getPhone()) | $tutor->getPerson()->getPhone() == '') ? 'No ha sido cargado' : $tutor->getPerson()->getPhone();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_birth_date">
    <div>
    <label for="birth_date"> <?php echo __('Fecha de nacimiento');?> </label>
      <?php echo $tutor->getPerson()->getBirthdate('d-m-Y');?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_birth_country">
    <div>
    <label for="birth_country"> <?php echo __('País de nacimiento');?> </label>
      <?php echo (is_null($tutor->getPerson()->getBirthCountry()) | $tutor->getPerson()->getBirthCountry() == '') ? 'No ha sido cargado' : $tutor->getPerson()->getBirthCountryRepresentation();?>    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_birth_state">
    <div>
    <label for="birth_state"> <?php echo __('Provincia de nacimiento');?> </label>
      <?php echo (is_null($tutor->getPerson()->getBirthState()) | $tutor->getPerson()->getBirthState() == '') ? 'No ha sido cargado' : $tutor->getPerson()->getBirthStateRepresentation();?>    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_birth_city">
    <div>
    <label for="birth_city"> <?php echo __('Ciudad de nacimiento');?> </label>
      <?php echo (is_null($tutor->getPerson()->getBirthCity()) | $tutor->getPerson()->getBirthCity() == '') ? 'No ha sido cargada' : $tutor->getPerson()->getBirthCityRepresentation();?>    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_tutor_type">
    <div>
    <label for="tutor_type"> <?php echo __('Tipo de tutor');?> </label>
      <?php echo (is_null($tutor->getTutorType()) | $tutor->getTutorType() == '') ? 'No ha sido cargado' : $tutor->getTutorType();?>    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_observations">
    <div>
    <label for="observations"> <?php echo __('Observaciones');?> </label>
      <?php echo (is_null($tutor->getPerson()->getObservations()) | $tutor->getPerson()->getObservations() == '') ? 'No han sido cargadas' : $tutor->getPerson()->getObservations();?>    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_username">
    <div>
    <label for="username"> <?php echo __('Nombre de usuario');?> </label>
      <?php echo (is_null($tutor->getPerson()->getsfGuardUser())) ? 'No posee usuario' : $tutor->getPerson()->getsfGuardUser()->getUsername();?>    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_username">
    <div>
    <label for="username"> <?php echo __('Occupation');?> </label>
      <?php echo (is_null($tutor->getOccupation()) | $tutor->getOccupation() == '') ? 'No ha sido cargada' : $tutor->getOccupation();?>    
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
</fieldset>