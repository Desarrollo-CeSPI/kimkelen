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
    <label for="descripction"> <?php echo __('Description');?> </label>
    <?php echo $medical_certificate->getDescription();?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="certificate"> <?php echo __('Certificate');?> </label>
    <?php echo ($medical_certificate->getCertificate()) ? link_to(__('Descargar documento adjunto'), 'medical_certificate/downloadCertificate?id='.$medical_certificate->getId(), array('target' => '_blank')) : ""; ?> 
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="certificate_status_id"> <?php echo __('Certificate status');?> </label>
    <?php echo BaseCustomOptionsHolder::getInstance('MedicalCertificateStatus')->getStringFor($medical_certificate->getCertificateStatusId()) . " " . format_date($medical_certificate->getDate(),'dd/MM/yyyy'); ?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="theoric_class"> <?php echo __('Theoric class');?> </label>
    <?php echo ($medical_certificate->getTheoricClass()) ? 'Sí' : 'No'?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="theoric_class_from"> <?php echo __('Theoric class from');?> </label>
    <?php echo (!is_null($medical_certificate->getTheoricClassFrom())) ? format_date($medical_certificate->getTheoricClassFrom(),'dd/MM/yyyy') : ''; ?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>
<div class="sf_admin_form_row sf_admin_Text">
  <div>
    <label for="theoric_class_to"> <?php echo __('Theoric class to');?> </label>
    <?php echo (!is_null($medical_certificate->getTheoricClassTo())) ? format_date($medical_certificate->getTheoricClassTo(),'dd/MM/yyyy') : ''; ?>
  </div>
  <div style="margin-top: 1px; clear: both;"></div>
</div>