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
<div class="header_student_card">
    <div class="header-img">
        <?php echo image_tag("logo-kimkelen-negro.png", array( 'class'=>'logo_student_card', 'absolute' => true)) ?> 
    </div> 
    <div class="header-img">
        <?php echo image_tag("unlp_logo.png", array( 'class'=>'logo_unlp_student_card', 'absolute' => true)) ?>
    </div> 
</div>
<div class="title">
    <div class='title-text'>
        <h3> <?php echo __('Student card')?> </h3>
    </div>
    <div class='title-text'>
        <?php $d =  $student->getLastStudentCareerSchoolYear()->getDivisions(); ?>
        <h3> <?php echo __('División: ') ?> <?php echo ($d[0]) ?  $d[0] :''?></h3>
    </div>
</div>


