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
 */
?>

<div class="col-md-4 person-container">
  <div class="row">
    <div class="col-md-12 personal-photo">
      <?php echo image_tag("frontend/user.svg", array('alt' => __('Foto'))); ?>
      <h1 class="person-name"><?php echo $person->getPerson()->getFullName(); ?></h1>
      <span class="person-document-type">DNI:</span>
      <span class="person-document-number"><?php echo $person->getPerson()->getIdentificationNumber(); ?></span>
    </div>
  </div>

  <div class="row">
    <div class=" col-md-12 personal-data">
      <div class="detail">
        <b> <?php echo __("Phone")?> </b>
        <span class="glyphicon glyphicon glyphicon-phone icon" aria-hidden="true"></span>
      </div>
      <p class="text"><?php echo ($person->getPerson()->getPhone()) ? $person->getPerson()->getPhone(): 'No posee'; ?> </p>

      <div class="detail">
        <b> <?php echo __("Email") ?> </b>
        <span class="glyphicon glyphicon glyphicon-envelope icon" aria-hidden="true"></span>
      </div>
      <p class="text"><?php echo ($person->getPerson()->getEmail()) ? $person->getPerson()->getEmail() : 'No posee'; ?></p>

      <div class="detail">
        <b> <?php echo __("Address")?> </b>
        <span class="glyphicon glyphicon glyphicon-map-marker icon" aria-hidden="true"></span>
      </div>
      <p class="text">
        <?php echo ($person->getPerson()->getAddress()) ? $person->getPerson()->getAddress()->getFullAddress() : 'No posee'; ?>
      </p>
    </div>
  </div>
</div>



		
