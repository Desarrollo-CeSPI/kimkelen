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
<div>
    <div class="header-text"> 
        <!-- IF ADEUDA MATERIAS -->
        <p>Para terminar sus estudios secundarios deberá aprobar: XXXXXXXXX materias </p>
        <!-- IF ADEUDA MATERIAS -->

        <p>Para que conste y a pedido del interesado, se expide el presente certificado confrontado 
        con los registros y actas originales por el Departamento de alumnos, en la ciudad de La Plata,
        a los <span><?php echo __("Dias") ?></span> dias del mes de <span><?php echo __("Mes") ?></span>
        del <span><?php echo __("Año") ?></span></p>
    </div>
</div>