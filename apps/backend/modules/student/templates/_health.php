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
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_blood_group">
    <div>
    <label for="blood_group"> <?php echo __('Blood group');?> </label>
      <?php echo (is_null($student->getBloodGroup()) | $student->getBloodGroup() == '') ? 'No ha sido cargado' : $student->getBloodGroup();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_blood_factor">
    <div>
    <label for="blood_factor"> <?php echo __('Blood factor');?> </label>
      <?php echo (is_null($student->getBloodFactor()) | $student->getBloodFactor() == '') ? 'No ha sido cargado' : $student->getBloodFactor();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_health_coverage">
    <div>
    <label for="health_coverage"> <?php echo __('Health coverage');?> </label>
      <?php echo (is_null($student->getHealthCoverage())) ? 'No ha sido cargado' : $student->getHealthCoverage();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>
  <div class="sf_admin_form_row sf_admin_Text sf_admin_form_emergency_information">
    <div>
    <label for="emergency_information"> <?php echo __('Emergency information');?> </label>
      <?php echo (is_null($student->getEmergencyInformation()) | $student->getEmergencyInformation() == '') ? 'No ha sido cargado' : $student->getEmergencyInformation();?>
    </div>
    <div style="margin-top: 1px; clear: both;"></div>
  </div>