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
<tr class="head" valign="bottom">
  <td align="center"  height="22" colspan="2"><?php echo __('Nivel: %nivel%', array('%nivel%' => $course->getYear())); ?> Div: <?php echo is_null($course->getDivision())? '' : $course->getDivision()->getDivisionTitle()?></td>
  <td align="center"  height="22" colspan="<?php echo $configuration->getCourseMarks() + 3 ?>">  <?php echo $course->getSubjectsStr() ?></td>
  <td align="center"  height="22" colspan="2">Profesor/a: <?php echo $course->getTeachersStr() ?></td>
</tr>
<tr class="head" valign="bottom">
  <td align="center"  height="22" colspan="2"><?php echo SchoolBehaviourFactory::getInstance()->getSchoolName()?></td>
  <td align="center"  height="22" colspan="<?php echo $configuration->getCourseMarks() + 3 ?>">Planilla de calificaciones <?php echo $course->getSchoolYear(); ?></td>
  <td align="center"  height="22" colspan="2">
  </td>
</tr>

<tr class="head" valign="bottom">
  <td align="center" width="40%" height="22" colspan="2"></td>
  <td align="center" width="18%" colspan="<?php echo $configuration->getCourseMarks()?>">Terminos</td>
  <td align="center" width="20%" >Promedio Anual</td>
  <td align="center" width="20%" colspan="3">Notas de examenes</td>
  <td align="center" width="10%">Calificacion</td>
  <td align="center" width="20%">Observaciones</td>
</tr>

<tr class="head" valign="bottom">
  <td align="center" width="4%" height="22"></td>
  <td align="center" width="36%" height="22"><?php echo __('Nombre y Apellido'); ?></td>

  <?php for($i = 1; $i <= $configuration->getCourseMarks(); $i++): ?>
    <td align="center"><?php echo $i; ?></td>
  <?php endfor; ?>

  <td align="center" width="15%" height="22"><?php echo __('Prom.'); ?></td>
  <td align="center" width="6%" height="22"> <?php echo __('Dic.'); ?></td>
  <td align="center" width="6%" height="22"><?php echo __('Feb.'); ?></td>
  <td align="center" width="8%" height="22"><?php echo __('Previous'); ?></td>
  <td align="center" width="10%" height="22"><?php echo __('Prom. Def.'); ?></td>
  <td align="center" width="20%" height="22"></td>
</tr>