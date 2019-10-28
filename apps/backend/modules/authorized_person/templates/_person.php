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
      <?php echo $authorized_person->getPerson()->getFullName();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_identification_type_and_number">
    <div>
    <label for="identification_type_and_number"> <?php echo __('Tipo y número de documento');?> </label>
      <?php echo BaseCustomOptionsHolder::getInstance('IdentificationType')->getStringFor($authorized_person->getPerson()->getIdentificationType()).': '.$authorized_person->getPerson()->getIdentificationNumber();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_family_relationship">
    <div>
    <label for="familiy_relationship"> <?php echo __('Family relationship');?> </label>
      <?php echo (!is_null($authorized_person->getFamilyRelationship()))? $authorized_person->getFamilyRelationship() :'No ha sido cargado' ;?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_telefono">
    <div>
    <label for="telefono"> <?php echo __('Teléfono');?> </label>
      <?php echo (is_null($authorized_person->getPerson()->getPhone()) | $authorized_person->getPerson()->getPhone() == '') ? 'No ha sido cargado' : $authorized_person->getPerson()->getPhone();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_telefono">
    <div>
    <label for="telefono_alternativo"> <?php echo __('Teléfono alternativo');?> </label>
      <?php echo (is_null($authorized_person->getPerson()->getAlternativePhone()) | $authorized_person->getPerson()->getAlternativePhone() == '') ? 'No ha sido cargado' : $authorized_person->getPerson()->getAlternativePhone();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
</fieldset>