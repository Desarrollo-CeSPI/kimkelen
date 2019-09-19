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
	<label class="text-section">Apellido y nombres: </label> <?php echo $student ?>    
    </div>
    <div class="field-section">
	<label class="text-section"> Edad: </label> <?php echo ".........."?>
        <label class="text-section"> Fecha de nacimiento: </label> <?php echo ($student->getPerson()->getBirthDate()) ? format_date($student->getPerson()->getBirthDate(), "dd/MM/yyyy") : ".............................." ?>
        <label class="text-section"> Lugar de nacimiento: </label> <?php echo ($student->getPerson()->getBirthCityRepresentation()) ? ucwords($student->getPerson()->getBirthCityRepresentation()) . ', ' .ucwords($student->getPerson()->getBirthStaterepresentation()) .', '. $student->getPerson()->getBirthCountryRepresentation() : ".............................."?> 
    </div>
    <div class="field-section">
        <label class="text-section"> Nacionalidad: </label> <?php echo ($student->getPerson()->getFullNationality())? strtolower($student->getPerson()->getFullNationality())  : ".............................."?>
        <label class="text-section"> DNI N°: </label> <?php echo $student->getPerson()->getIdentificationNumber() ?>
    </div>
    <div class="field-section">
	<label class="text-section"> Domicilio: </label> 
        <label class="text-section"> Calle: </label> <?php echo ($student->getPerson()->getAddress()->getStreet()) ? $student->getPerson()->getAddress()->getStreet() : ".............................."?>
        <label class="text-section"> N°: </label> <?php echo ($student->getPerson()->getAddress()->getNumber()) ? $student->getPerson()->getAddress()->getNumber() : ".........."?>
    </div>
    <div class="field-section"> 
        <label class="text-section"> Piso: </label> <?php echo ($student->getPerson()->getAddress()->getFloor()) ? $student->getPerson()->getAddress()->getFloor() : ".........."?>
        <label class="text-section"> Dto: </label> <?php echo ($student->getPerson()->getAddress()->getFlat()) ? $student->getPerson()->getAddress()->getFlat() : ".........."?>
        <label class="text-section"> Localidad: </label> <?php echo ($student->getPerson()->getAddress()->getCity()) ? $student->getPerson()->getAddress()->getCity() : ".............................."?>
        <label class="text-section"> C.P.: </label>
    </div>
    <div class="field-section"> 
        <label class="text-section"> Teléfono (no celular): </label> <?php echo ($student->getPerson()->getPhone()) ? $student->getPerson()->getPhone() : ".............................."?>
        <label class="text-section"> Teléfono alternativo: </label> <?php echo ".............................."?>
    </div>
    <div class="field-section"> 
        <label class="text-section"> Correo electrónico: </label> <?php echo ($student->getPerson()->getEmail()) ? $student->getPerson()->getEmail() : ".............................................................................................................."?>
    </div>
</div>

<?php foreach ($student->getStudentTutors() as $st):?>
    <div class="title-section">
        <?php echo __('Datos de la madre, padre o tutor legal')?>
    </div>
    <div class="body-section">
            <div class="field-section">
                <label class="text-section"> Apellido y nombres: </label> <?php echo ".........................................................................................." ?>    
                <label class="text-section"> ¿Vive?: </label> <?php echo "SI (   ) NO (   )"?>
            </div>
            <div class="field-section">
                <label class="text-section"> Nacionalidad: </label> <?php echo ".........................................."?>
                <label class="text-section"> Documento: </label> 
                <label class="text-section"> Tipo: </label><?php echo "...................." ?>
                <label class="text-section"> Número: </label><?php echo "..........................." ?>
            </div>
            <div class="field-section">
                <label class="text-section"> Domicilio: </label> 
                <label class="text-section"> Calle: </label> <?php echo ".........................................."?> 
                <label class="text-section"> e/ </label><?php echo "..........................................." ?>
                <label class="text-section"> N° </label><?php echo "............................" ?>
            </div>
            <div class="field-section"> 
                <label class="text-section"> Piso: </label> <?php echo "...................."?>
                <label class="text-section"> Dto: </label> <?php echo  "...................."?>
                <label class="text-section"> Localidad: </label> <?php echo ".................................................."?>
                <label class="text-section"> C.P.: </label> <?php echo ".................."?>
            </div>
            <div class="field-section"> 
                <label class="text-section"> Teléfono (no celular): </label> <?php echo "..............................."?>
                <label class="text-section"> Teléfonos alternativos (consignar varios): </label> <?php echo ".............................."?>
            </div>
            <div class="field-section"> 
                <?php echo "........................................................"?>
                <label class="text-section"> Correo electrónico: </label> <?php echo ".............................................................."?>
            </div>
            <div class="field-section"> 
                <label class="text-section"> Ocupación: </label> <?php echo "................................................................................................................................."?>
            </div>
        </div>
<?php endforeach;?>

<?php if (count($student->getStudentTutors()) < 2): ?>
    <?php for($i = count($student->getStudentTutors()) ; $i < 2 ; $i++):?>
        <div class="title-section">
            <?php echo __('Datos de la madre, padre o tutor legal')?>
        </div>
        <div class="body-section">
            <div class="field-section">
                <label class="text-section"> Apellido y nombres: </label> <?php echo ".........................................................................................." ?>    
                <label class="text-section"> ¿Vive?: </label> <?php echo "SI (   ) NO (   )"?>
            </div>
            <div class="field-section">
                <label class="text-section"> Nacionalidad: </label> <?php echo ".........................................."?>
                <label class="text-section"> Documento: </label> 
                <label class="text-section"> Tipo: </label><?php echo "...................." ?>
                <label class="text-section"> Número: </label><?php echo "..........................." ?>
            </div>
            <div class="field-section">
                <label class="text-section"> Domicilio: </label> 
                <label class="text-section"> Calle: </label> <?php echo ".........................................."?> 
                <label class="text-section"> e/ </label><?php echo "..........................................." ?>
                <label class="text-section"> N° </label><?php echo "............................" ?>
            </div>
            <div class="field-section"> 
                <label class="text-section"> Piso: </label> <?php echo "...................."?>
                <label class="text-section"> Dto: </label> <?php echo  "...................."?>
                <label class="text-section"> Localidad: </label> <?php echo ".................................................."?>
                <label class="text-section"> C.P.: </label> <?php echo ".................."?>
            </div>
            <div class="field-section"> 
                <label class="text-section"> Teléfono (no celular): </label> <?php echo "..............................."?>
                <label class="text-section"> Teléfonos alternativos (consignar varios): </label> <?php echo ".............................."?>
            </div>
            <div class="field-section"> 
                <?php echo "........................................................"?>
                <label class="text-section"> Correo electrónico: </label> <?php echo ".............................................................."?>
            </div>
            <div class="field-section"> 
                <label class="text-section"> Ocupación: </label> <?php echo "................................................................................................................................."?>
            </div>
        </div>
    <?php endfor;?>
<?php endif; ?>

<div class="box-student-card">
    Las firmas registradas serán las que deban rubricar la documentación enviada por el establecimiento. 
</div>




