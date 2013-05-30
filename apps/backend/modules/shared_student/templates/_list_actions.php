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
  <li class="sf_admin_action_back">
    <?php $reference_array = sfContext::getInstance()->getUser()->getReferenceFor("shared_student"); ?>
    <li class="sf_admin_action_back"><?php echo link_to((isset($reference_array["back_to_label"]))?__($reference_array["back_to_label"]):__('Volver al listado de años lectivos'), 'shared_student/back') ?></li>
  </li>

<?php echo $helper->linkToUserExport(array(  'params' =>   array(  ),  'class_suffix' => 'user_export',  'label' => 'User export',)) ?>
