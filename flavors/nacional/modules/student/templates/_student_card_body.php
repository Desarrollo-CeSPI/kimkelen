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
<?php include_partial('personal_data_student_card',array('student'=> $student)) ?>

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

    <?php $school_name = SchoolBehaviourFactory::getInstance()->getSchoolName(); ?>
    <p class="field-section">¿Autoriza a su hijo/a a ser fotografiado/a y/o filmado/a para la página web del <?php echo $school_name ?>
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
</div>
