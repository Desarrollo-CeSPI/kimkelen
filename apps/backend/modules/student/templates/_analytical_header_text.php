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
 *  */ 
// @TODO: Agregar nacionalidad (por el momento se tiene el dato de pais de nacimiento). Cambiar "nacido en" por "de nacionalidad"
// @TODO: Agregar escuela de origen (origin_school de student?)
?>
<?php use_helper('Date') ?>
<div>
    <p class="header-text"> El/La director/a del
        <span><?php echo __($career_student->getCareer()->getCareerName()) ?></span>
        de la <?php echo __("Universidad Nacional de La Plata") ?> CERTIFICA que
        <strong><?php echo $student ?></strong> <strong><?php echo BaseCustomOptionsHolder::getInstance('IdentificationType')->getStringFor($student->getPerson()->getIdentificationType()) ?> <?php echo $student->getPerson()->getIdentificationNumber() ?></strong> sexo <strong><?php echo BaseCustomOptionsHolder::getInstance('SexType')->getStringFor($student->getPerson()->getSex()) ?></strong>
        nacido/a en <span><?php echo ucwords($student->getPerson()->getBirthCityRepresentation()); ?>, <?php echo ucwords($student->getPerson()->getBirthStaterepresentation()); ?>, <?php echo $student->getPerson()->getBirthCountryRepresentation() ?></span>,
        el día <strong><?php echo format_date($student->getPerson()->getBirthDate(), "D") ?></strong>,
        que ingresó en este establecimiento en el año <span><?php echo $student->getInitialSchoolYear()->getYear(); ?></span>
        proveniente del Colegio <span><?php //echo $student->getOriginSchool(); ?></span> donde finalizó sus estudios de <?php echo __('nombre_ultimo_anio_primario'); ?> y aprobó en este Establecimiento las asignaturas que con sus respectivas notas se expresan:
    </p>
</div>