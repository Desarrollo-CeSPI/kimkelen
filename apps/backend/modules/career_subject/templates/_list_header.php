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
<h3>Plan de estudio: <?php echo $pager->getParameter('career')?></h3>
<div>
    <p>Una vez que el plan de estudio no es modificable, las materias del mismo no se podrán cambiar debido a que ya existen años lectivos o incluso alumnos inscriptos en él.</br>
			 Sólo será posible crear nuevas materias que sean opcionales, las materias optativas con sus opciones se editarán desde cada <strong><?php echo link_to("año lectivo",'@school_year')?></strong>.
		</p>
</div>
