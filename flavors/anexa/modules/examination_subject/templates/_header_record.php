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
<div class="record-header">
      <div class="logo"><?php echo image_tag("logo-kimkelen-negro.png", array('absolute' => true,'class' => 'logo_print_record')) ?></div>
  </div>  
  <div style="text-align: center">
     <h2>Acta de Examen</h2>
  </div>
  <div class="white-background"><?php echo 'Exámenes de Alumnos: ' . $examination_subject->getExamination() ; ?></div>
  <div class="gray-background">
      <strong><?php echo __('Subject'); ?></strong>:
      <strong><?php echo $examination_subject->getSubject() . ' - ' . $examination_subject->getYear() . ' año'  ?></strong>
      
      <span class="right">
          <strong><?php echo __('School year'); ?></strong>: <?php echo $examination_subject->getExamination()->getSchoolYear() ?> 
          <strong> <?php echo __('Day')?>:</strong><?php echo  (!is_null($examination_subject->getDate())) ? ' ' . date_format(new DateTime($examination_subject->getDate()), "d/m/Y") : ' _____ / _____ / _____' ?>  &nbsp; </span>
  </div>
  
  <div class="gray-background">
    <span><strong><?php echo 'Acta N°: '; ?></strong>  <?php echo $record->getId(); ?> </span>
    <span class="right"><strong><?php echo 'Tomo: '; ?></strong><?php echo ($rs->getBook()) ? $rs->getBook() : ' _______________________ '; ?>     <strong> <?php echo 'Folio físico: '; ?></strong><?php echo ($rs->getPhysicalSheet())? $rs->getPhysicalSheet() : ' ________ '; ?></span>
  </div>