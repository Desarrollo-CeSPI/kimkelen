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
<?php use_helper('Javascript', 'Object', 'I18N', 'Form') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php $last_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($career_school_year->getSchoolYear()); ?>
<div id="sf_admin_container">
  <h1><?php echo __('Matricular egresados de otra carrera en %career_school_year%', array('%career_school_year%' => $career_school_year->__toString())) ?></h1>

  <h2>Esta acción efectúa la matriculación de alumnos que egresaron de una carrera de la institución y deben articular con el principio de otra carrera.</h2>
  <h3>Se tomará a todos los egresados <?php echo $last_school_year ?> de la carrera que elija a continuación y los matriculará en el año inicial de <?php echo $career_school_year ?></h3>
  <div id="sf_admin_content">
    <form action="<?php echo url_for('career_school_year/saveMatriculateGraduatedFromOtherCareer') . '?id='.$career_school_year->getId();?>" method="post">
      <fieldset>
        <?php echo $form ?>
      </fieldset>

      <ul class="sf_admin_actions">
        <li><?php echo link_to(__('Volver al listado de carreras'), '@career_school_year', array('class' => 'sf_admin_action_go_back')) ?></li>
        <li><input type="submit" value="<?php echo __('Guardar'); ?>" /></li>
      </ul>
    </form>
  </div>