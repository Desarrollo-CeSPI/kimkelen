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
<?php use_helper('I18N','Object','Form');?>
<?php include_partial('career/assets') ?>

<?php $career = CareerPeer::RetrieveByPk($sf_user->getAttribute('career_id'));?>
<?php $students = CareerStudentPeer::retrieveStudentsForCareer($sf_user->getAttribute('career_id'));?>

<div id="sf_admin_container">
  <div class="career_view_title">
    <h1><?php echo $career->getCareerName().' | '.$career->getPlanName()?></h1>
    <h2><?php echo __('Alumnos Inscriptos')?>:</h2>
     <table class="sf_career_table">
       <thead>
         <tr>
           <th><?php echo __("Apellido");?>:</th>
           <th><?php echo __("Nombre");?>:</th>
         </tr>
       </thead>
       <tbody>
         <tr>
           <?php if (empty($students)):?>
             <td>
              <h3> <?php echo __("No existen alumnos inscriptos en la carrera")?> </h3>
             </td>
             <td></td>
           <?php else:?>
             <?php foreach ($students as $student):?>
                <td> <?php echo $student->getFirstName();?> </td>
                <td> <?php echo $student->getLastName();?> </td>
               </tr>
             <?php endforeach;?>
           <?php endif;?>
       </tbody>
     </table>
  </div>

  <br>
  <td>
      <ul class="sf_admin_td_actions">
      <li class="sf_admin_action_career_view">
        <?php echo link_to(__('Volver al listado de carreras', array(), 'messages'), '@career' , array()) ?>
      </li>
    </ul>
  </td>
  <br>

</div>