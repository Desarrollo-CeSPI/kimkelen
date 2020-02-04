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
      





		
