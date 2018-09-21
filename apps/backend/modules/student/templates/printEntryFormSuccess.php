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

<?php use_stylesheet('/css/report-card.css') ?>
<?php use_helper('Date') ?>
<div class="certificate-wrapper">
    <div class="report-content_student">
        <?php include_partial('certificate_header');?>
        <div class="header_text_title">
            <div class="student_title"><?php echo $student->getPerson()->getFullName()?> </strong></div>
            <div><?php echo $student->getPerson()->getFullIdentification()?></div>
            <div><b><?php echo __('Career year') . ': ' ?></b><?php echo ($student->getCurrentOrLastStudentCareerSchoolYear()) ?  $student->getCurrentOrLastStudentCareerSchoolYear()->getYear() . '° ' . __('Year'): ''?></div>
        </div>
        <div><b>Nacionalidad:</b> <?php echo ($student->getPerson()->getFullNationality()) ? $student->getPerson()->getFullNationality() : ''?></div>
        <div><b>Sexo:</b> <?php echo ($student->getPerson()->getSex()) ? BaseCustomOptionsHolder::getInstance('SexType')->getStringFor($student->getPerson()->getSex()) : ''?></div>
        <div><b>Datos de nacimiento:</b></div>
        <div class="tab_div">
            <div>Fecha: <?php echo format_date($student->getPerson()->getBirthDate(), "dd/MM/yyyy") ?></div>
            <div>Localidad:<span><?php echo ucwords($student->getPerson()->getBirthCityRepresentation()); ?>, <?php echo ucwords($student->getPerson()->getBirthStaterepresentation()); ?>, <?php echo ucwords($student->getPerson()->getBirthCountryrepresentation()); ?></span></div>
        </div>
        <div><b>Domicilio:</b></div>
        <div class="tab_div">
            <div>Calle: <?php echo $student->getPerson()->getAddress()?> </div>
            <div>Localidad: <span><?php echo $student->getPerson()->getAddress()->getCity() .', '. $student->getPerson()->getAddress()->getState() . ', '. $student->getPerson()->getAddress()->getState()->getCountry()?>  </span></div>
            <div>Teléfonos: <?php echo $student->getPerson()->getPhone()?> </div>
        </div>
        <div><b>Estudios:</b></div>
        <div class="tab_div">
            <div>Escuela de procedencia: <?php echo $student->getOriginSchool()?> </div>
            <?php if ($student->getOriginSchool()):?>
            <div>Ubicación: <span><?php echo $student->getOriginSchool()->getCity() ?></span></div>
            <?php endif ;?>
        </div>
        <div><b>Datos familiares</b></div>
        <?php foreach ($student->getStudentTutors() as $student_tutor) :?>
            <div><b>Padre/madre o tutor:</b> <?php echo $student_tutor->getTutor()->getPerson()->getFullName()?></div>
            <div class="tab_div">
                <div>Vive: <?php echo ($student_tutor->getTutor()->getIsAlive())? 'Sí' : 'No'?> </div>
                <div>Domicilio: <span><?php echo $student_tutor->getTutor()->getPerson()->getAddress() ?></span></div>
                <div>Localidad: <span><?php echo $student_tutor->getTutor()->getPerson()->getAddress()->getCity() .', '. $student_tutor->getTutor()->getPerson()->getAddress()->getState() .', '. $student_tutor->getTutor()->getPerson()->getAddress()->getState()->getCountry()?></span></div>
                <div>Teléfono: <?php echo $student_tutor->getTutor()->getPerson()->getPhone()?> </div>
                <div>Email: <?php echo $student_tutor->getTutor()->getPerson()->getEmail()?> </div>
                <div>Nacionalidad: <?php echo $student_tutor->getTutor()->getPerson()->getFullNationality()?> </div>
            </div>
        <?php endforeach;?>
    </div>
</div>


