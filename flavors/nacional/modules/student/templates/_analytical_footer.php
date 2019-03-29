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

<?php $cs = CareerSubjectPeer::retrieveByPK(CareerSubject::TALLER_S_NACIO);?>
<div id="analytical_footer" class="misma_pagina">
    <?php include_partial('analytical_footer_text', array('student' => $career_student->getStudent(), 'career_student' => $career_student, 'analytical' => $analytical)) ?>
    <div class="analytical-form">
        <?php if (!is_null($cs) && $career_student->getStudent()->hasApprovedCareerSubject($cs)):?>
        <span id="subject_observations">: Taller de Sexualidad: 10 encuentros de 60 minutos-materia sin calificaciones-</span>
        <?php endif;?>
   <?php echo $form ?>
    </div>       
    <div class="analytical-observations">
        <?php if(!is_null($cs) && $career_student->getStudent()->hasApprovedCareerSubject($cs)): ?>
        <div class="footer-text">Observaciones: Taller de Sexualidad: 10 encuentros de 60 minutos-materia sin calificaciones- <?php echo (isset($analytic) && $analytic->getObservations()) ? $analytic->getObservations() : '' ?> </div>
        <?php endif;?>
    </div>   
    <?php include_partial('analytical_footer_signatures', array('student' => $career_student->getStudent(), 'career_student' => $career_student, 'analytical' => $analytical)) ?> 
</div>