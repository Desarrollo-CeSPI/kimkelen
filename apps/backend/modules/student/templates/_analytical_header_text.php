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
    <p class="header-text"> La directora del 
    	<span><?php echo __($career_student->getCareer()->getCareerName()) ?></span>
        de la Universidad Nacional de La Plata CERTIFICA que 
        <span><?php echo $student ?></span> 
        DNI <span><?php echo $student->getPerson()->getIdentificationNumber() ?></span> 
        de nacionalidad 
        <span><?php echo __("Una nacionalidad") ?></span>, 
        sexo <span><?php echo BaseCustomOptionsHolder::getInstance('SexType')->getStringFor($student->getPerson()->getSex()) ?></span>
        nacido en <span><?php echo $student->getPerson()->getBirthCityRepresentation() ?></span>
        provincia de <span><?php echo $student->getPerson()->getBirthStaterepresentation() ?></span>,
        el <span><?php echo format_date($student->getPerson()->getBirthDate(),"D") ?></span>,
        que ingreso en este establecimiento en el año <span><?php echo __("XXXX") ?></span>
        proveniente del Colegio 
 		<span><?php echo __("XXXXXXXXXXXXXX") ?></span>

</div>