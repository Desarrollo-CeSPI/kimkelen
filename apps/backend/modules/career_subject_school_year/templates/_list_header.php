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
<h3>Carrera: <?php echo $pager->getParameter('career_school_year')?></h3>
<div>
    <p>- Cambiando la configuración de una materia se sobreescribe la configuración global del plan de estudios.</p>
</div>
<ul class="sf_admin_actions">
  <li class="sf_admin_action_go_back">
    <?php echo link_to(__("Volver al listado"), "@career_school_year") ?>
  </li>
</ul>
