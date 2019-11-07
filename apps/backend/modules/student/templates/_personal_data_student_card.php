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
<?php use_helper('Date') ?>
<div class="title-section">
    <?php echo __('Datos del alumno')?>
</div>
<div class="body-section">
    <div class="field-section">
        <div class="col col_100">
            <label class="text-section">Apellido y nombres: </label> <?php echo $student ?>
        </div>
    </div>
    <div class="field-section">
        <div class="col col_10">
            <label class="text-section"> Edad: </label> <?php echo ($student->getPerson()->getBirthdate()) ? $student->getYearsOld() :".............." ?>
        </div>
        <div class="col col_30">       
            <label class="text-section"> Fecha de nacimiento: </label> <?php echo ($student->getPerson()->getBirthdate()) ? format_date($student->getPerson()->getBirthDate(), "dd/MM/yyyy") : ".............." ?>
        </div>
        <div class="col col_60"> 
            <label class="text-section"> Lugar de nacimiento: </label> 
            <?php echo ($student->getPerson()->getBirthCityRepresentation()) ? ucwords($student->getPerson()->getBirthCityRepresentation()) . ', ' .ucwords($student->getPerson()->getBirthStaterepresentation()) .', '. $student->getPerson()->getBirthCountryRepresentation() : "............................................................" ?> 
        </div>
    </div>
    <div class="field-section">
        <div class="col col_60">
            <label class="text-section"> Nacionalidad: </label> <?php echo ($student->getPerson()->getFullNationality()) ? strtolower($student->getPerson()->getFullNationality()) : ".............................."?>
        </div>
        <div class="col col_40">   
            <label class="text-section"> DNI N°: </label> <?php echo $student->getPerson()->getIdentificationNumber() ?>
        </div>
    </div>
    <div class="field-section">
        <div class="col col_80">
            <label class="text-section"> Domicilio: </label> 
            <label class="text-section"> Calle: </label> <?php echo ($student->getPerson()->getAddress() && $student->getPerson()->getAddress()->getStreet()) ? $student->getPerson()->getAddress()->getStreet() : "................................................................................................"?>
        </div> 
        <div class="col col_20">
            <label class="text-section"> N°: </label> <?php echo ($student->getPerson()->getAddress() && $student->getPerson()->getAddress()->getNumber()) ? $student->getPerson()->getAddress()->getNumber() : "......................." ?>
        </div>
    </div>
    <div class="field-section">
        <div class="col col_20">
            <label class="text-section"> Piso: </label> <?php echo ($student->getPerson()->getAddress() && $student->getPerson()->getAddress()->getFloor()) ? $student->getPerson()->getAddress()->getFloor() : "....................."?> 
        </div>
        <div class="col col_20">
            <label class="text-section"> Dto: </label> <?php echo ($student->getPerson()->getAddress() && $student->getPerson()->getAddress()->getFlat()) ? $student->getPerson()->getAddress()->getFlat() : "....................."?>
        </div>     
        <div class="col col_40">
            <label class="text-section"> Localidad: </label> <?php echo($student->getPerson()->getAddress() && $student->getPerson()->getAddress()->getCity()) ? $student->getPerson()->getAddress()->getCity() : "..........................................."?>
        </div>
        <div class="col col_20">
            <label class="text-section"> C.P.: </label> <?php echo ($student->getPerson()->getAddress() && $student->getPerson()->getAddress()->getPostalCode())? $student->getPerson()->getAddress()->getPostalCode() : "....................."?>
        </div> 
    </div>
    <div class="field-section"> 
        
        <div class="col col_40">
            <label class="text-section"> Teléfono (no celular): </label> <?php echo ($student->getPerson()->getPhone()) ? $student->getPerson()->getPhone() : "............................."?>  
        </div>
        <div class="col col_60">
            <label class="text-section"> Teléfonos alternativos: </label> <?php echo ($student->getPerson()->getAlternativePhone()) ? $student->getPerson()->getAlternativePhone(): ".........................................................."?>
        </div>
    </div>
    <div class="field-section"> 
        <div class="col col_100">
            <label class="text-section"> Correo electrónico: </label> <?php echo ($student->getPerson()->getEmail()) ? $student->getPerson()->getEmail() : ".........................................................................................................................."?>
        </div>
    </div>
</div>