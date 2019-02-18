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
<?php include_partial("student/assets") ?>
<?php use_stylesheet('report-card.css', 'first', array('media' => 'screen')) ?>
<?php use_stylesheet('analytics.css', 'last', array('media' => 'all')) ?>
<?php use_stylesheet('print-analytics.css', 'last', array('media' => 'print')) ?>
<div id="sf_admin_container">
    <div id="screen_header">
        <h1> <?php echo __("Analytical for %student%", array('%student%' => $career_student->getStudent())) ?></h1>
        <h2> <?php echo __("Study plan %career%", array('%career%' => $career_student->getCareer())) ?></h2>
    </div>

    <ul class="sf_admin_actions">
        <li class="sf_admin_action_list">
            <?php echo link_to(__('Volver al listado alumnos', array(), 'messages'), "@student", array()) ?>
        </li>
        <li class="sf_admin_action_print">
            <?php echo link_to(__('Pantalla de  impresión', array(), 'messages'), 'student/printAnalytical?id=' . $career_student->getId().'&orientation=Portrait&page_size=Legal') ?>
        </li>
    </ul>
    
<div class="analytical">
    <?php include_partial('analytical_header', array('career_student' => $career_student, 'analytical' => $analytical)) ?>
    <div class="report-content">
        <?php include_component('student', 'component_analytical_table', array('career_student' => $career_student)) ?>
    </div>
    <?php include_partial('analytical_footer', array('career_student' => $career_student, 'analytical' => $analytical)) ?>
</div>    
    
    <ul class="sf_admin_actions">
        <li class="sf_admin_action_list">
            <?php echo link_to(__('Volver al listado alumnos', array(), 'messages'), "@student", array()) ?>
        </li>
        <li class="sf_admin_action_print">
            <?php echo link_to(__('Pantalla de  impresión', array(), 'messages'), 'student/printAnalytical?id=' . $career_student->getId().'&orientation=Portrait') ?>
        </li>
    </ul>
</div>


<div id="sf_admin_container">

</div>
