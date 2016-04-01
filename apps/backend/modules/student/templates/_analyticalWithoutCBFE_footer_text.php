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

<?php use_helper('Date') ?>

    <div class="header-text">
		<p>Año de ingreso: <?php echo $student->getInitialSchoolYear()->getYear(); ?>.</p>
        <?php if ($analytical->has_completed_career()): ?>
            <p>Fecha de egreso:<?php echo format_datetime($analytical->get_last_exam_date()->format('U'), "D"); ?>.</p>
        <?php else: ?>
	        <p>Certificado de Estudios Incompleto.</p>
        <?php endif; ?>
        <p>Se extiende el presente certificado, confrontado con los registros y actas originales por los señores Secretario Administrativo y Jefe de la Oficina de Alumnos. En la ciudad de <?php echo __('escuela_ciudad'); ?>, a los <?php echo date('d'); ?> días del mes de <?php echo format_date(time(), 'MMMM'); ?> del año <?php echo date('Y'); ?>.</p>
    </div>
