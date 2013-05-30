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

<div style="float: left; margin-right: 10px;margin-top: 6px"><?php echo $absence_type->getOrder() ?></div>
<div >
  <div> <?php echo link_to1("<img src=" . image_path('control-090.png') . ">", "absence_type/incrementOrder?id=" . $absence_type->getId()) ?></div>
  <div> <?php echo link_to1("<img src=" . image_path('control-270.png') . ">", "absence_type/decrementOrder?id=" . $absence_type->getId()) ?></div>
</div>
