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
<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_postal_address">
  <div>
  <label for="tutor_postal_address"> <?php echo __("Postal Address");?> </label>
  <?php if(is_null($tutor->getPersonAddress()->getStreet())):?>
    <strong><?php echo __('No hay dirección cargada')?></strong>
  <?php else:?>
        <table border="1">
          <tr>
            <th><?php echo __("Calle");?>         </th>
            <th><?php echo __("Número");?>        </th>
            <th><?php echo __("Piso");?>          </th>
            <th><?php echo __("Departamento");?>  </th>
            <th><?php echo __("Ciudad");?>        </th>
            <th><?php echo __("Provincia");?>     </th>
          </tr>
          <tr>
            <td><?php echo ($tutor->getPersonAddress()->getStreet());?>   </td>
            <td><?php echo ($tutor->getPersonAddress()->getNumber());?>   </td>
            <td><?php echo ($tutor->getPersonAddress()->getFloor());?>    </td>
            <td><?php echo ($tutor->getPersonAddress()->getFlat());?>     </td>
            <td><?php echo ($tutor->getPersonAddress()->getCity());?>     </td>
            <td><?php echo ($tutor->getPersonAddress()->getState());?>    </td>
          </tr>
        </table>
   <?php endif?>
  </div>
  <div style="margin-top: 1px; clear: both;">
  </div>
</div>