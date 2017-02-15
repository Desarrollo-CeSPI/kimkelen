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

	<div class="col-md-8">
		<div class="row title-box">
            <div class="col-md-12 title-icon">
                <?php echo image_tag("/frontend/images/student-hat.svg", array('alt' => __('ícono'))); ?>
			    <span class="title-text"> <?php echo __("Students in charge");?> </span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php foreach ($students as $s): ?>
                    <div class="col-md-4 container-student">
                        <?php include_partial('student/student_info', array('student' => $s)) ?>
                        <?php include_partial('student/student_actions', array('student' => $s)) ?>
                    </div>
                <?php endforeach;?>
            </div>
        </div>
	</div>
