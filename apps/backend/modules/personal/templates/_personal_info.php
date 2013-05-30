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
<?php use_helper('Javascript') ?>
<!--Mostramos si tiene username -->
<div>
  <span class="attribute <?php is_null($username = $personal->getUsername()) and print 'disabled' ?>">Acceso al sistema <?php echo is_null($username = $personal->getUsername())?'':$username?></span>
</div>
<!--Mostramos si esta Activo  -->
<div>
  <span class="attribute <?php !$personal->getIsActive() and print 'inactive' ?>"><?php echo $personal->getIsActive()?'Activo':'Inactivo'?></span>
</div>
