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

<div class="row">
    <div class="col-md-12 student-photo">
        <?php echo image_tag("/frontend/images/user.svg", array('alt' => __('Student'))); ?>
        <h1 class="student-name"><?php echo $student->getPerson()->getFullName(); ?></h1>
        <span class="student-document-type">DNI:</span>
        <span class="student-document-number"><?php echo $student->getPerson()->getIdentificationNumber(); ?></span>
    </div>
</div>
<div class="row">
    <div class="col-md-12 personal-data">
        <div class="detail">
            <b> <?php echo __("Phone")?> </b>
            <span class="glyphicon glyphicon glyphicon-phone icon" aria-hidden="true"></span>
        </div>
        <p class="text"><?php echo ($student->getPerson()->getPhone()) ? $student->getPerson()->getPhone(): 'No posee'; ?> </p>

        <div class="detail">
            <b> <?php echo __("Email") ?> </b>
            <span class="glyphicon glyphicon glyphicon-envelope icon" aria-hidden="true"></span>
        </div>
        <p class="text"><?php echo ($student->getPerson()->getEmail()) ? $student->getPerson()->getEmail() : 'No posee'; ?></p>

        <div class="detail">
            <b> <?php echo __("Address")?> </b>
            <span class="glyphicon glyphicon glyphicon-map-marker icon" aria-hidden="true"></span>
        </div>
        <p class="text"><?php echo ($student->getPerson()->getAddress()) ? $student->getPerson()->getAddress()->getFullAddress() : 'No posee'; ?></p>
    </div>
</div>

