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

<?php foreach ($student->getStudentTutors() as $st):?>
    <?php include_partial('tutors_student_card',array('tutor'=> $st->getTutor())) ?>
<?php endforeach;?>

<?php if (count($student->getStudentTutors()) < 2): ?>
    <?php for($i = count($student->getStudentTutors()) ; $i < 2 ; $i++):?>
        <?php include_partial('tutors_student_card',array('tutor'=> NULL)) ?>
    <?php endfor;?>
<?php endif; ?>

<div class="box-student-card">
    Las firmas registradas serán las que deban rubricar la documentación enviada por el establecimiento. 
</div>

<div class="title-section">
    <?php echo __('Personas autorizadas a retirar al alumno/a (Deberán ser mayores de 18 años)')?>
</div>

<table class="table-student-card">
    <thead>
        <tr>
            <th class="row-large"><?php echo __('Nombre y Apellido')?></th>
            <th class=""><?php echo __('Documento (Tipo y N°)')?></th>
            <th class="row-short"><?php echo __('Parentesco')?></th>
            <th class="row-short"><?php echo __('Teléfono particular')?></th>
            <th class="row-short"><?php echo __('Teléfono celular')?></th>
            <th class="row-short"><?php echo __('Firma')?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($student->getStudentAuthorizedPersons() as $sap): ?>
        <tr>
            <td><?php echo $sap->getAuthorizedPerson()->getPersonFullname();?></td>
            <td><?php echo $sap->getAuthorizedPerson()->getPersonFullIdentification();?></td>
            <td><?php echo $sap->getAuthorizedPerson()->getFamilyRelationship();?></td>
            <td><?php echo $sap->getAuthorizedPerson()->getPerson()->getPhone();?></td>
            <td><?php echo $sap->getAuthorizedPerson()->getPerson()->getAlternativePhone();?></td>
            <td></td>
        </tr>
        <?php endforeach; ?>
        <?php if (count($student->getStudentAuthorizedPersons()) < 4): ?>
            <?php for($i = count($student->getStudentAuthorizedPersons()) ; $i < 4 ; $i++):?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            <?php endfor;?>
        <?php endif; ?> 
    </tbody>
</table>

<div class="text-information">
    <p class="field-section text-section"> Las personas autorizadas para el retiro de los alumnos únicamente podrán ser las designdas por la madre, 
    padre o tutor en esta planilla y al momento del retiro deberán acreditar la identidad con el DNI.
    </p>

    <p class="field-section text-section text-declaration"> Declaro conocer y aceptar las condiciones establecidas en el Reglamento para los Colegios de Pregrado de la UNLP
    y los requisitos necesarios para justificar inasistencias por razones de salud.
    </p>

    <div class="title-section">
        Marcar lo que corresponda
    </div>

    <p class="field-section">¿Autoriza a su hijo/a a ser fotografiado/a y/o filmado/a para la página web del Colegio Nacional "Rafael Hernández" 
    en actividades con fines educativos?
     <span class="options">  
         <?php if(!is_null($student->getPhotosAuthorization())):?>
            <?php echo ($student->getPhotosAuthorization()) ? ' SI ( X  ) NO (  )' : ' SI (  ) NO ( X )' ?>
         <?php else:?>
            <?php echo ' SI (  ) NO (  )' ?> 
         <?php endif;?>
     </span>
    </p>

    <p class="field-section">¿Autoriza a su hijo/a a ingresar al establecimiento después del horario de entrada o retirarse del establecimiento
        antes del horario habitual de salida, ante la ausencia del profesor correspondiente? 
        <span class="options">  
         <?php if(!is_null($student->getWithdrawalAuthorization())):?>
            <?php echo ($student->getWithdrawalAuthorization()) ? ' SI ( X  ) NO (  )' : ' SI (  ) NO ( X )' ?>
         <?php else:?>
            <?php echo ' SI (  ) NO (  )' ?> 
         <?php endif;?>
        </span>
    </p>

    <p class="field-section text-section">
        La modificación de la autorización de ingreso y de retiro del alumno deberá realizarse mediante la
        concurrencia del padre, madre o tutor quien deberá completar nuevamente la planilla.
    </p>
    <p class="field-section text-section">
        Se informa a los Sres. Padres, madres o tutores legales que cualquier cambio temporal y/o permanente
        del orden personal, familiar, socioeconómico, legal y/o de salud del alumno/a deberá ser notificado a la
        institución a la mayor brevedad posible con caracter obligatorio.
    </p>
</div>

<div class="signatures">
    <div class="col col_40"> 
        Firma y aclaración de madre, padre o tutor legal
    </div>
    <div class="col col_40"> 
        Firma y aclaración de madre, padre o tutor legal
    </div>
</div>
