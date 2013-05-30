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
<?php echo 'Persona ' . $person ?>
<table border="1">
  <tr>
    <td>
      <?php foreach ($person->getTeachers() as $teacher): ?>
        <br><strong><?php echo 'Grupo: Profesor' ?></strong>
        <?php echo link_to(' Ver perfil', '@teacher_show?id=' . $teacher->getId(), array('target' => '_blank')); ?><br>
      <?php endforeach; ?>

      <?php foreach ($person->getPersonals() as $personal): ?>

        <br><strong><?php echo 'Grupo: ' . PersonalType::toString($personal->getPersonalType()); ?></strong>
        <?php echo link_to(' Ver perfil', $personal->retrieveRouteForShow() . '?id=' . $personal->getId(), array('target' => '_blank')); ?><br><br>

      <?php endforeach; ?>
    </td>
  </tr>