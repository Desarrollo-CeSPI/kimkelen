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


<div id="analytical_footer" class="misma_pagina">
    <?php if(isset($form)):?>
        <?php include_partial('analytical_footer_text', array('student' => $career_student->getStudent(), 'career_student' => $career_student, 'analytical' => $analytical,'form'=> $form)) ?>
    <?php else:?>
            <?php include_partial('analytical_footer_text', array('student' => $career_student->getStudent(), 'career_student' => $career_student, 'analytical' => $analytical)) ?>
    <?php endif;?>
    <div class="analytical-form">
        <?php echo (isset($form) ? $form['observations']->renderRow() : '') ?> 
         <div class="analytical-observations">
             <?php if(isset($analytic) && $analytic->getObservations()): ?>
             <div class="footer-text">Observaciones: <?php echo $analytic->getObservations() ?> </div>
             <?php endif;?>
         </div>   
    </div> 
     <?php include_partial('analytical_footer_signatures', array('student' => $career_student->getStudent(), 'career_student' => $career_student, 'analytical' => $analytical)) ?>
</div>