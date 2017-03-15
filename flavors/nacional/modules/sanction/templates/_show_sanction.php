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
 <div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="number"> <?php echo __('Number');?> </label>
    <?php echo $student_disciplinary_sanction->getNumber();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
 <div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="student"> <?php echo __('Student');?> </label>
    <?php echo $student_disciplinary_sanction->getStudent(); ?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="request_date"> <?php echo __('Request date');?> </label>
    <?php echo $student_disciplinary_sanction->getFormattedRequestDate();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="resolution_date"> <?php echo __('Resolution date');?> </label>
    <?php echo $student_disciplinary_sanction->getResolutionDate();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="school_year"> <?php echo __('School year');?> </label>
    <?php echo $student_disciplinary_sanction->getSchoolYear();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="applicant"> <?php echo __('Applicant');?> </label>
    <?php echo $student_disciplinary_sanction->getApplicant();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="responsible"> <?php echo __('Responsible');?> </label>
    <?php echo $student_disciplinary_sanction->getResponsible();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="sanction_type"> <?php echo __('Sanction type');?> </label>
    <?php echo $student_disciplinary_sanction->getSanctionType();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="disciplinary_sanction_type"> <?php echo __('Disciplinary sanction type');?> </label>
    <?php echo $student_disciplinary_sanction->getDisciplinarySanctionType();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="value"> <?php echo __('Value');?> </label>
    <?php echo $student_disciplinary_sanction->getValue();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="observation"> <?php echo __('Observation');?> </label>
    <?php echo $student_disciplinary_sanction->getObservation();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<?php include_partial('document_show', array('student_disciplinary_sanction'=> $student_disciplinary_sanction)); ?>


