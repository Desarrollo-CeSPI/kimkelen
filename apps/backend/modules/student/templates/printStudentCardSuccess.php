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
<?php include_partial("student/assets") ?>
<?php use_stylesheet('student-card.css', 'first', array('media' => 'screen')) ?>
   

    <?php include_partial('student_card_header',array('student'=> $student)) ?>
    <?php include_partial('student_card_body',array('student'=> $student)) ?>
    <div class="report-content">
        <?php //include_component('student', 'component_analytical_table', array('career_student' => $career_student)) ?>
    </div>
    <?php //include_partial('student_card_footer', array('career_student' => $career_student, 'analytical' => $analytical, 'form'=> $form)) ?>
 
