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
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_name">
  <div>
    <label for="name"> <?php echo __('Name');?> </label>
    <?php echo $division;?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_school_year">
  <div>
    <label for="school_year"> <?php echo __('School year');?> </label>
    <?php echo $division->getSchoolYear();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_career">
  <div>
    <label for="career"> <?php echo __('Career');?> </label>
    <?php echo $division->getCareer();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_year">
  <div>
    <label for="year"> <?php echo __('Year');?> </label>
    <?php echo $division->getYear();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_preceptors">
  <div>
    <label for="preceptor"> <?php echo __('Preceptors');?> </label>
    <?php echo $division->getPreceptorsString() ?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>