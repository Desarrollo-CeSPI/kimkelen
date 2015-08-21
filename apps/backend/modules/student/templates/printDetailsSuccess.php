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
<div style="font-size: medium">
  <h2><?php echo SchoolBehaviourFactory::getInstance() ?></h2>
  <hr>

  <?php /* @var $student Student */ ?>
  <div class="student_name_lastname">
    Apellido y nombre: <?php echo $student; ?>
  </div>

  <div class="student_current_division">
    Año y Division:
    <?php foreach ($student->getCurrentDivisions() as $division): ?>
      <?php echo $division . ' ' ?>
    <?php endforeach; ?>
  </div>
  <hr>

  DNI: <?php echo $student->getPerson()->getIdentificationNumber() ?> <br>
  Legajo: <?php echo $student->getGlobalFileNumber() ?> <br>
  Informacion de emergencia: <?php echo $student->getEmergencyInformation() ?><br>
  Fecha de nacimiento: <?php echo $student->getPerson()->getBirthdate('d/m/Y') ?><br>
  Lugar de nacimiento: <?php echo CityPeer::retrieveByPK($student->getPerson()->getBirthCity()) ?><br>
  <hr>

  Hermanos :
  <?php foreach ($student->getBrothers() as $brother): ?>
    <?php echo $brother . ' ' ?>
  <?php endforeach; ?> <br>
  <hr>

  Tutores:
  <?php foreach ($student->getStudentTutors() as $student_tutor): ?>
    <?php echo $student_tutor . ' ' ?>
    Telefono: <?php echo $student_tutor->getPhone() . ' ' ?>
    Ocupación: <?php echo $student_tutor->getOcupation() . ' ' ?>
    <br>
  <?php endforeach; ?>
  <hr>

  factor sanguineo : <?php echo $student->getBloodFactor(); ?> <br>
  Grupo sanguineo : <?php echo $student->getBloodGroup(); ?> <br>


  <hr>
</div>
<br><br><br><br>
<ul class="sf_admin_actions">
  <li >
    <a href="#" onclick="imprimir()"><?php echo __('Imprimir detalle') ?></a><br>
    <a href="<?php echo url_for('student/index') ?>"><?php echo __('Go back') ?></a><br>
  </li>
</ul>
<script type="text/javascript">
  function imprimir()
  {
    jQuery(".sf_admin_actions").hide(300,function() {
      window.print();
      jQuery(".sf_admin_actions").show();
    });


  }
</script>