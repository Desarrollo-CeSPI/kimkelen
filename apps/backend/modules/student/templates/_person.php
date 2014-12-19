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
<h2><?php echo __('Personal data') ?> </h2>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_full_name">
  <div>
    <label for="full_name"> <?php echo ('Nombre completo'); ?> </label>
    <?php echo $student->getPerson()->getFullName(); ?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_identification_type_and_number">
  <div>
    <label for="identification_type_and_number"> <?php echo __('Tipo y número de documento'); ?> </label>
    <?php echo BaseCustomOptionsHolder::getInstance('IdentificationType')->getStringFor($student->getPerson()->getIdentificationType()) . ': ' . $student->getPerson()->getIdentificationNumber(); ?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_sex_type">
  <div>
    <label for="sex_type"> <?php echo __('Sexo'); ?> </label>
    <?php echo BaseCustomOptionsHolder::getInstance('SexType')->getStringFor($student->getPerson()->getSex()); ?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>


<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_origin_school">
    <div>
        <label for="origin_school"> <?php echo __('Origin school'); ?> </label>
        <?php echo ($student->getOriginSchool())? $student->getOriginSchool(): '-'; ?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
</div>

<?php if ($student->getEducationalDependency()): ?>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_educational_dependency">
    <div>
      <label for="educational_dependency"> <?php echo __('Educational dependency'); ?> </label>
      <?php echo $student->getEducationalDependency(); ?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
<?php endif; ?>

<?php if ($student->getGlobalFileNumber()): ?>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_global_file_number">
    <div>
        <label for="global_file_number"> <?php echo __('Global file number'); ?> </label>
        <?php echo $student->getGlobalFileNumber(); ?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
</div>
<?php endif; ?>

<?php if (!$sf_user->hasCredential('show_student_min')): ?>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_email">
    <div>
      <label for="email"> <?php echo __('Email'); ?> </label>
      <?php echo (is_null($student->getPerson()->getEmail()) | $student->getPerson()->getEmail() == '') ? 'No ha sido cargado' : $student->getPerson()->getEmail(); ?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_telefono">
    <div>
      <label for="telefono"> <?php echo __('Teléfono'); ?> </label>
      <?php echo (is_null($student->getPerson()->getPhone()) | $student->getPerson()->getPhone() == '') ? 'No ha sido cargado' : $student->getPerson()->getPhone(); ?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
<?php endif ?>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_birth_date">
  <div>
    <label for="birth_date"> <?php echo __('Fecha de nacimiento'); ?> </label>
    <?php echo $student->getPerson()->getBirthdate('d-m-Y'); ?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_birth_country">
  <div>
    <label for="birth_country"> <?php echo __('Lugar de nacimiento'); ?> </label>
    <?php echo (is_null($student->getPerson()->getCity()) | $student->getPerson()->getCity() == '') ? 'No ha sido cargada' : $student->getPerson()->getCity(); ?>
    , <?php echo (is_null($student->getPerson()->getBirthState()) | $student->getPerson()->getBirthState() == '') ? 'No ha sido cargado' : $student->getPerson()->getBirthState(); ?>
    , <?php echo (is_null($student->getPerson()->getBirthCountry()) | $student->getPerson()->getBirthCountry() == '') ? 'No ha sido cargado' : $student->getPerson()->getBirthCountry(); ?></div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_address">
  <div>
    <label for="address"> <?php echo __('Address'); ?> </label>
    <?php echo (is_null($student->getPerson()->getAddress()) | $student->getPerson()->getAddress() == '') ? 'No ha sido cargado' : $student->getPerson()->getAddress(); ?>    </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>

<?php include_partial('personal/person_photo', array('object' => $student)); ?>

<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_observations">
  <div>
    <label for="observations"> <?php echo __('Observaciones'); ?> </label>
    <?php echo (is_null($student->getPerson()->getObservations()) | $student->getPerson()->getObservations() == '') ? 'No han sido cargadas' : $student->getPerson()->getObservations(); ?>    </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<h2><?php echo __('System access') ?> </h2>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_username">
  <div>
    <label for="username"> <?php echo __('Nombre de usuario'); ?> </label>
    <?php echo (is_null($student->getPerson()->getsfGuardUser())) ? 'No posee usuario' : $student->getPerson()->getsfGuardUser()->getUsername(); ?>    </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>