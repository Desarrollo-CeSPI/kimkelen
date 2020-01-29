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
<?php $evaluator_instance = SchoolBehaviourFactory::getEvaluatorInstance();?>
<table class="gridtable_bordered">
    <thead>
      <tr class="printColumns">
        <th class="orden" rowspan="2"><?php echo __('N°'); ?> </th>
        <th rowspan="2"><?php echo __('Apellido y Nombre'); ?></th>
        <th class="division_record" rowspan="2"><?php echo __('Division'); ?></th>
        <?php if ( ! is_null($record->getTotalMarks())) : ?>
        <th colspan="<?php echo $record->getTotalMarks()?>"><?php echo __('Términos'); ?></th>
        <?php endif;?>
        <th colspan="2"><?php echo __('Mark'); ?></th>
        <th class="result_record" rowspan="2"><?php echo __('Resultado'); ?></th>
      </tr>
      <tr>          
        <?php if ( ! is_null($record->getTotalMarks())) : ?>
            <?php for ($i = 1; $i <= $record->getTotalMarks(); $i++):?>
                <th class="period_record" colspan="1"><?php echo $i ?></th>
            <?php endfor; ?>
        <?php endif;?>
        <th class="calification_number" colspan="1"><?php echo __('Números'); ?></th>
        <th class="calification_letter" colspan="1"><?php echo __('Letras'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; ?>
      <?php foreach ($record->getRecordDetailsForSheet($rs->getSheet()) as $rd): ?>
        <tr>
          <td class="orden"><?php echo $rd->getLine() ?> </td>
          <td class="student"><?php echo $rd->getStudent() ?></td>
          <td> <?php echo $rd->getDivision() ?> </td>
          <?php if ( ! is_null($rd->getPartialMarks())) : ?>
            <?php $partial_marks = unserialize($rd->getPartialMarks()) ?>
            <?php for ($i = 1; $i <= $record->getTotalMarks(); $i++):?>
                <td class="calification number"> <?php echo $partial_marks[$i];?></td>
            <?php endfor; ?>
        <?php endif;?>
          <td class="calification number"><?php echo ($rd->getMark())? $rd->getMark(): ''; ?></td>
          <td class="calification text">
            <?php $c = new num2text();?>
            <?php if(!$rd->getIsAbsent()):?>
              
              <?php $c = new num2text();
                $mark = $rd->getMark();
                $mark_parts = explode(',', $mark);
                
                if (1 === count($mark_parts))
                {
                    $mark_parts = explode('.', $mark);    
                }
                $mark_symbol = (1 === count($mark_parts)) ? trim($c->num2str($mark_parts[0])) :trim($c->num2str($mark_parts[0])) . ('00' !== $mark_parts[1]?','.$mark_parts[1]:''); ?>

               <?php echo $mark_symbol; ?>
              <?php else: ?>
                <?php echo __('Absent'); ?>
              <?php endif; ?>
          </td>
          <td> <?php echo $evaluator_instance->getResultStringFor($rd->getResult()) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
