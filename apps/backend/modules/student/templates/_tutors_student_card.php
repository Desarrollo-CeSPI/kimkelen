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
<div class="title-section">
    <?php echo __('Datos de la madre, padre o tutor legal')?>
</div>
<div class="body-section">
    <div class="field-section">
        
        <div class="col col_80"> 
            <label class="text-section"> Apellido y nombres: </label> 
            <?php echo ($tutor) ? $tutor->getPerson()->getFullName() : "............................................................................................" ?>
        </div>
        <div class="col col_20">
            <label class="text-section"> ¿Vive?: </label> 
            <?php if ($tutor):?>
                <?php if ($tutor->getIsAlive()):?>
                    <?php echo "SI ( X ) NO (   )"?>
                <?php else: ?>
                    <?php echo "SI (   ) NO ( X )"?>
                <?php endif; ?>
            <?php else: ?>
                <?php echo "SI (   ) NO (   )"?>
            <?php endif; ?>
        </div>
    </div>
    <div class="field-section">
        <div class="col col_40"> 
            <label class="text-section"> Nacionalidad: </label> <?php echo ($tutor && $tutor->getPerson()->getFullNationality())? strtolower($tutor->getPerson()->getFullNationality()) : "......................................."?>
        </div>
        <div class="col col_60">        
            <label class="text-section"> Documento: </label> 
            <label class="text-section"> Tipo: </label><?php echo ($tutor && $tutor->getPerson()->getIdentificationType()) ? BaseCustomOptionsHolder::getInstance('IdentificationType')->getStringFor($tutor->getPerson()->getIdentificationType()) : ".................." ?>
            <label class="text-section"> Número: </label><?php echo ($tutor && $tutor->getPerson()->getIdentificationNumber())? $tutor->getPerson()->getIdentificationNumber() : "................................." ?>
        </div>
    </div>
    <div class="field-section">
        <div class="col col_80">
            <label class="text-section"> Domicilio: </label>
            <label class="text-section"> Calle: </label><?php echo ($tutor && $tutor->getPerson()->getAddress() && $tutor->getPerson()->getAddress()->getStreet()) ? $tutor->getPerson()->getAddress()->getStreet(): "............................................................................................."?> 
        </div>
        <div class="col col_20">
            <label class="text-section"> N° </label><?php echo ($tutor && $tutor->getPerson()->getAddress() && $tutor->getPerson()->getAddress()->getNumber()) ? $tutor->getPerson()->getAddress()->getNumber() : "........................" ?>
        </div>
    </div>
    <div class="field-section">
        <div class="col col_20">
            <label class="text-section"> Piso: </label> <?php echo ($tutor && $tutor->getPerson()->getAddress() && $tutor->getPerson()->getAddress()->getFloor()) ? $tutor->getPerson()->getAddress()->getFloor() : "....................."?> 
        </div>
        <div class="col col_20">
            <label class="text-section"> Dto: </label> <?php echo ($tutor && $tutor->getPerson()->getAddress() && $tutor->getPerson()->getAddress()->getFlat()) ? $tutor->getPerson()->getAddress()->getFlat() : "....................."?>
        </div>
        <div class="col col_40">
            <label class="text-section"> Localidad: </label> <?php echo($tutor && $tutor->getPerson()->getAddress() && $tutor->getPerson()->getAddress()->getCity()) ? $tutor->getPerson()->getAddress()->getCity() : "..........................................."?>
        </div>
        <div class="col col_20">
            <label class="text-section"> C.P.: </label> <?php echo ($tutor && $tutor->getPerson()->getAddress() && $tutor->getPerson()->getAddress()->getPostalCode())? $tutor->getPerson()->getAddress()->getPostalCode() : "....................."?>
        </div>
    </div>
    <div class="field-section"> 
        <div class="col col_40">
            <label class="text-section"> Teléfono (no celular): </label> <?php echo ($tutor && $tutor->getPerson()->getPhone()) ?$tutor->getPerson()->getPhone() : "............................."?>  
        </div>
        <div class="col col_60">
            <label class="text-section"> Teléfonos alternativos: </label> <?php echo ($tutor && $tutor->getPerson()->getAlternativePhone()) ? $tutor->getPerson()->getAlternativePhone(): ".........................................................."?>
        </div>
    </div>
    <div class="field-section">
        <div class="col col_100">
            <label class="text-section"> Correo electrónico: </label> <?php echo ($tutor && $tutor->getPerson()->getEmail()) ? $tutor->getPerson()->getEmail():".........................................................................................................................."?>
        </div>
    </div>
    <div class="field-section"> 
        <div class="col col_100">
            <label class="text-section"> Ocupación: </label> <?php echo ($tutor && $tutor->getOccupation()) ? $tutor->getOccupation() : "....................................................................................................................................."?>
        </div>
    </div>
</div>
