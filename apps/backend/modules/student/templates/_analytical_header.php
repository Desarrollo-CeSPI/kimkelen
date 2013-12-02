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

<div class="header_row">
  
  <div class="colsleft">
        <dl class="dl-horizontal" >
          <dt><?php echo __("Legajo N°")?>:</dt>
          <dd><?php echo $career_student->getStudent()->getGlobalFileNumber() ?></dd>
          <dt><?php echo __("Course")?>:</dt>
          <dd><?php echo $career_student->getStudent()->getGlobalFileNumber() ?></dd>
        </dl>
  </div>
   
  <div class="colscenter">
    <div class="colsleft logo" style="  width: 100px !important;">
      <?php echo image_tag("kimkelen_logo_small.png", array('absolute' => true)) ?>
    </div>
    <div class="colsleft">
      <h3 > <?php echo __($career_student->getCareer()->getCareerName()) ?></h3>
      <p > <?php echo __("Universidad Nacional de La Plata") ?></p>
    </div>
  </div>

  <div class="colsright">      
    <dl class="col-lg-3 dl-horizontal" >
          <dt><?php echo __("Certificado N°")?>:</dt>
          <dd><?php echo $career_student->getStudent()->getFolioNumber() ?></dd>
    </dl>
  </div>
</div>
<div class="header_row">
  <?php include_partial('analytical_header_text', array('student' => $career_student->getStudent(), 'career_student' => $career_student )) ?>
</div>