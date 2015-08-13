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

<div id="sf_admin_container">
    <div>
        <h1> <?php echo __("Analytical for %student%", array('%student%' => $career_student->getStudent())) ?></h1>
        <h2> <?php echo __("Study plan %career%", array('%career%' => $career_student->getCareer())) ?></h2>
    </div>
</div>
<ul class="sf_admin_actions">
    <li class="sf_admin_action_list">
        <?php echo link_to(__('Volver al listado alumnos', array(), 'messages'), '@student', array()) ?>
    </li>
</ul>
<div class="analytical">
    <?php include_partial('analytical_header', array('career_student' => $career_student)) ?>
    <div class="report-content">
        <?php include_component('student', 'component_analytical_table', array('career_student' => $career_student)) ?>
    </div>
    <?php include_partial('analytical_footer', array('career_student' => $career_student, 'analytical' => $analytical)) ?>
</div>
<div id="sf_admin_container">
    <ul class="sf_admin_actions">
        <li class="sf_admin_action_list">
            <?php echo link_to(__('Volver al listado alumnos', array(), 'messages'), "@student", array()) ?>
        </li>
        <li class="sf_admin_action_print">
            <?php echo link_to(__('Pantalla de  impresión', array(), 'messages'), 'student/printAnalytical?id=' . $career_student->getId()) ?>
        </li>
    </ul>
</div>