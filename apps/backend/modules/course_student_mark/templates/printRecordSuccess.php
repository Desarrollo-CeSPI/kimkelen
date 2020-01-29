<?php /*
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
<?php use_stylesheet('examination-record.css') ?>
<?php use_stylesheet('print-examination-record.css', 'last', array('media' => 'print')) ?>

<?php $evaluator_instance = SchoolBehaviourFactory::getEvaluatorInstance();?>
<?php foreach ($record->getRecordSheets() as $rs):?>
<div class="record-wrapper">
  <?php include_partial('header_record', array('record' => $record , 'rs' => $rs,'cs' => $cs))?>
  <?php include_partial('table_record', array('record' => $record , 'rs' => $rs,'cs' => $cs))?>
  <?php include_partial('footer_record', array('record' => $record , 'rs' => $rs))?>
</div>
<div style="page-break-before: always;"></div>
<?php endforeach;?>